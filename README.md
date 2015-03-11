# nbp-php
API for accessing NBP (Narodowy Bank Polski) currecy rates in PHP

## Usage
Note: currently only average rate is retrieved.
#### Getting the average rate from specific day example
```php
$nbp = new NbpRepository();
$currencyData = $nbp->getRate('2015-01-02', 'USD');
var_dump($currencyData->avg);
var_dump($currencyData->date);
```
Outputs:
```
double(3.5725)
string(10) "2015-01-02"
```
#### Getting the average rate from first date before specified date example
This is usefull when you need to retrieve last available currency rate from working day before specified date.
```php
$nbp = new NbpRepository();
$currencyData = $nbp->getRateBefore('2015-01-02', 'USD');
var_dump($currencyData->avg);
var_dump($currencyData->date);
```
Outputs:
```
double(3.5072)
string(10) "2014-12-31"
```
#### Using cache example
When using cache the amount of HTTP requests to NBP server is minimized.
```php
<?php
use Doctrine\Common\Cache\FilesystemCache;
use MaciejSz\NbpPhp\Service\NbpCache;
use MaciejSz\NbpPhp\Service\NbpRepository;

$cacheBackend = new FilesystemCache(sys_get_temp_dir() . "/nbp-php");
$nbpCache = new NbpCache($cacheBackend);

$nbp = new NbpRepository($nbpCache);
// ...
```