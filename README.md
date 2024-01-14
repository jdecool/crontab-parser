Crontab Parser
==============

A simple crontab parser written in PHP.

## Installation

Install it with [Composer](https://getcomposer.org/):

```bash
$ composer require jdecool/crontab-parser
```

## Getting started

```php
<?php

$content = <<<CRONTAB
# m h  dom mon dow   command
* * * * *   /usr/bin/php /path/to/script.php
0 0 * * *   /usr/bin/php /path/to/other/script.php
CRONTAB;

$parser = JDecool\CrontabParser\CrontabParser();
$crontab = $parser->parse($content); // return a `JDecool\CrontabParser\Crontab` instance
```
