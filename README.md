# Patcher

[![Build Status](https://travis-ci.org/icewind1991/patcher.svg?branch=master)](https://travis-ci.org/icewind1991/patcher)
[![Code Coverage](https://scrutinizer-ci.com/g/icewind1991/patcher/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/icewind1991/patcher/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/icewind1991/patcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/icewind1991/patcher/?branch=master)

Replace build in php functions

## Usage

### Overwriting a method

```php
use Icewind\Patcher\Patcher;

$patcher = new Patcher();
$patcher->patchMethod('time', function () {
    return 100;
});
$patcher->whiteListDirectory(__DIR__ . '/src');
$patcher->autoPatch();

include 'src/....';
```


### Using the original method

```php
use Icewind\Patcher\Patcher;

$patcher = new Patcher();
$patcher->patchMethod('time', function ($method, $arguments, $original) {
    $time = $original();
    error_log("Time: $time");
    return $time;
});
$patcher->whiteListDirectory(__DIR__ . '/src');
$patcher->autoPatch();

include 'src/....';
```

## API

- `patchMethod(string $method, callable $handler)`: Set the handler for a method
 - The handler will be called with the following three arguments
  - `string $method` the name of the method being called
  - `array $arguments` the arguments the method was called with
  - `callable $original` a closure which will call the overwriten method with the correct arguments and return the result
- `whiteListDirectory(string $path)`: Add a directory to the whitelist for the auto patcher
- `patchForNamespace(string $namespace)`: Apply all patched methods to a namespace
- `autoPatch()`: Enable auto patching for all files included from this point
 - Automatically apply the patch methods for any namespace defined
 - Will only be applied for files within a whitelisted directory
