# Patcher

[![Build Status](https://travis-ci.org/icewind1991/patcher.svg?branch=master)](https://travis-ci.org/icewind1991/patcher)
[![Code Coverage](https://scrutinizer-ci.com/g/icewind1991/patcher/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/icewind1991/patcher/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/icewind1991/patcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/icewind1991/patcher/?branch=master)

Replace build in php functions

## Usage

```php
use Icewind\Patcher\Patcher;

$patcher = new Patcher();
$patcher->patchMethod('time', function () {
    return 100;
});
$patcher->whiteListDirectory(__DIR__ . '/src);
$patcher->autoPatch();

include 'src/....';
```
