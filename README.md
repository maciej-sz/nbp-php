# nbp-php

API for accessing NBP (Narodowy Bank Polski) currecy rates in PHP

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![No dependencies][ico-no-deps]][link-packagist]

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
use MaciejSz\NbpPhp\NbpRepository;

$cacheBackend = new FilesystemCache(sys_get_temp_dir() . "/nbp-php");
$nbpCache = new NbpCache($cacheBackend);

$nbp = new NbpRepository($nbpCache);
// ...
```

[ico-version]:https://img.shields.io/packagist/v/maciej-sz/nbp-php.svg?style=plastic
[ico-travis]:https://img.shields.io/travis/maciej-sz/nbp-php/master.svg?style=plastic
[ico-no-deps]:https://img.shields.io/badge/dependencies-none-brightgreen.svg?style=plastic

[link-packagist]:https://packagist.org/packages/maciej-sz/nbp-php
[link-travis]:https://travis-ci.org/maciej-sz/nbp-php
