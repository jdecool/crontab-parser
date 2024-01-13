<?php

namespace JDecool\CrontabParser;

use Cron\CronExpression;

final class Cron
{
    public static function create(string $expression, string $command): self
    {
        return new self(
            new CronExpression($expression),
            $command,
        );
    }

    public function __construct(
        public readonly CronExpression $expression,
        public readonly string $command,
    ) {
    }
}
