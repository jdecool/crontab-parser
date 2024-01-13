<?php

namespace JDecool\CrontabParser\Tests;

use Cron\CronExpression;
use JDecool\CrontabParser\Cron;
use JDecool\CrontabParser\Crontab;
use JDecool\CrontabParser\CrontabParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CrontabParserTest extends TestCase
{
    #[DataProvider('povideStringCrontab')]
    public function testParseEmptyString(string $crontab, Crontab $expected): void
    {
        $parser = new CrontabParser();

        self::assertEquals($expected, $parser->fromString($crontab));
    }

    /**
     * @return iterable<string, array{string, Crontab}>
     */
    public static function povideStringCrontab(): iterable
    {
        yield 'empty crontab' => [
            '',
            new Crontab(),
        ];

        yield '@daily' => [
            '@daily echo "Hello world" > /dev/null',
            new Crontab([
                new Cron(new CronExpression('@daily'), 'echo "Hello world" > /dev/null'),
            ]),
        ];

        yield '0 1 2 3 4 /usr/bin/find' => [
            '0 1 2 3 4 /usr/bin/find',
            new Crontab([
                new Cron(new CronExpression('0 1 2 3 4'), '/usr/bin/find'),
            ]),
        ];

        yield '* * * * * rm -rf /tmp/folder' => [
            '* * * * * rm -rf /tmp/folder',
            new Crontab([
                new Cron(new CronExpression('* * * * *'), 'rm -rf /tmp/folder'),
            ]),
        ];

        yield '*/2 * * * * echo "Foo"' => [
            '*/2 * * * * echo "Foo"',
            new Crontab([
                new Cron(new CronExpression('*/2 * * * *'), 'echo "Foo"'),
            ]),
        ];

        yield '5 7-23/4 * * *' => [
            '5 7-23/4 * * *',
            new Crontab([
                new Cron(new CronExpression('5 7-23/4 * * *'), ''),
            ]),
        ];

        yield 'expression with trailing spaces' => [
            '  *   *            *         *      *      rm -rf /tmp/folder',
            new Crontab([
                new Cron(new CronExpression('* * * * *'), 'rm -rf /tmp/folder'),
            ]),
        ];

        yield 'comment is ignored' => [
            '# /etc/cron.d/mycron: crontab entries',
            new Crontab(),
        ];

        yield 'environment variable is ignored' => [
            'SHELL=/bin/bash',
            new Crontab(),
        ];

        yield 'multiline crontab' => [
            <<<CRONTAB
@daily echo "Hello world" > /dev/null
0 1 2 3 4 /usr/bin/find
* * * * * rm -rf /tmp/folder
CRONTAB,
            new Crontab([
                new Cron(new CronExpression('@daily'), 'echo "Hello world" > /dev/null'),
                new Cron(new CronExpression('0 1 2 3 4'), '/usr/bin/find'),
                new Cron(new CronExpression('* * * * *'), 'rm -rf /tmp/folder'),
            ]),
        ];

        yield 'multiline crontab mixed every elements' => [
            <<<CRONTAB

# /etc/cron.d/anacron: crontab entries for the anacron package

SHELL=/bin/sh

30 7-23 * * *   root	[ -x /etc/init.d/anacron ] && if [ ! -d /run/systemd/system ]; then /usr/sbin/invoke-rc.d anacron start >/dev/null; fi
@daily      rm -rf /tmp/path


CRONTAB,
            new Crontab([
                new Cron(new CronExpression('30 7-23 * * *'), 'root	[ -x /etc/init.d/anacron ] && if [ ! -d /run/systemd/system ]; then /usr/sbin/invoke-rc.d anacron start >/dev/null; fi'),
                new Cron(new CronExpression('@daily'), 'rm -rf /tmp/path'),
            ]),
        ];
    }
}
