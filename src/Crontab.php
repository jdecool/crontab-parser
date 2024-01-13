<?php

namespace JDecool\CrontabParser;

final class Crontab
{
    /**
     * @param Cron[] $cronjobs
     */
    public function __construct(
        public readonly array $cronjobs = [],
    ) {
    }
}
