<?php

namespace JDecool\CrontabParser;

use Cron\CronExpression;

class CrontabParser
{
    private const CRONJOB_PATTERN = '/^(\s*(?:\S+\s+){4}\S+)(?:\s+(.+))?$/';
    private const NON_STANDARD_PATTERN = '/^(@\S+)\s+(.+)$/';
    private const COMMENT_PATTERN = '/^\s*#/';
    private const EMPTY_LINE_PATTERN = '/^\s*$/';
    private const ENVIRONMENT_VARIABLE_PATTERN = '/^\s*(\S+)=(\S+)\s*$/';

    public function fromString(string $content): Crontab
    {
        $cronjobs = $this->parseCronjobs($content);

        return new Crontab($cronjobs);
    }

    /**
     * @return Cron[]
     */
    private function parseCronjobs(string $content): array
    {
        $cronjobs = [];

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (
                preg_match(self::COMMENT_PATTERN, $line)
                || preg_match(self::EMPTY_LINE_PATTERN, $line)
                || preg_match(self::ENVIRONMENT_VARIABLE_PATTERN, $line)
            ) {
                continue;
            }

            $cronjobs[] = $this->parseLine($line);
        }

        return $cronjobs;
    }

    private function parseLine(string $line): Cron
    {
        preg_match(self::NON_STANDARD_PATTERN, $line, $matches);
        if (isset($matches[1])) {
            return $this->createCronFromMatches($matches);
        }

        preg_match(self::CRONJOB_PATTERN, $line, $matches);
        if (isset($matches[1])) {
            return $this->createCronFromMatches($matches);
        }

        throw new \RuntimeException("Line '$line' could not be parsed.");
    }

    /**
     * @param string[] $matches
     */
    private function createCronFromMatches(array $matches): Cron
    {
        if (!isset($matches[1])) {
            throw new \RuntimeException('Invalid matches.');
        }

        return new Cron(
            new CronExpression($matches[1]),
            $matches[2] ?? '',
        );
    }
}
