# Patcher
 
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
