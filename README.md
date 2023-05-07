# nbp-php

API for accessing Polish National Bank (NBP - Narodowy Bank Polski) currency and commodities exchange rates.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![No dependencies][ico-no-deps]][link-packagist]
[![MIT License][ico-license]][link-license]

## Usage

### Installing via composer

```composer require maciej-sz/nbp-php```

### Minimal setup

```php
<?php
require_once 'vendor/autoload.php';

$currencyAverages = CurrencyAverageRatesService::new();
$rate = $currencyAverages->fromDayBefore('2023-01-01')->fromTable('A')->getRate('USD');

$rate->getValue();         // 4.4018
$rate->getEffectiveDate(); // 2022-12-30
$rate->getCurrencyName();  // dolar amerykański
```

## Services

### CurrencyAverageRatesService

This service provides API for accessing average rates published in NBP tables.

#### `fromMonth` method

Returns flat collection of rates from all NBP tables at given month

Example:
```php
$averageRatesFromJanuary = $currencyAverages->fromMonth(2023, 1);
foreach ($averageRatesFromJanuary as $rate) {
    echo "{$rate->getCurrencyCode()} rate from {$rate->getEffectiveDate()} is {$rate->getValue()}\n";
}
```
Outputs:
```
THB rate from 2023-01-02 is 0.1267
USD rate from 2023-01-02 is 4.3811
AUD rate from 2023-01-02 is 2.9767
...
```

#### `fromDay` method

Returns a dictionary with NBP tables from given day.

Example:
```php
$eurRateFromApril4th = $currencyAverages
    ->fromDay('2023-04-04')
    ->fromTable('A')
    ->getRate('EUR');

echo $eurRateFromApril4th->getValue(); // 4.6785
```

#### `fromDayBefore` method

Returns a dictionary with NBP tables from day before given day. This method can be useful
in some bookkeeping applications when there is a legislatory need to calculate
transfer prices. The legislation requires for the prices to be calculated using 
currency rate applied in the business day before the actual transfer date. Which is
exactly what this method exposes.

Example:
```php
$eurRateFromBeforeJanuary2nd = $currencyAverages
    ->fromDayBefore('2023-01-02')
    ->fromTable('A')
    ->getRate('EUR');

echo $eurRateFromBeforeJanuary2nd->getEffectiveDate()->format('Y-m-d');
echo $eurRateFromBeforeJanuary2nd->getCurrencyName();
echo $eurRateFromBeforeJanuary2nd->getValue();
```
Outputs:
```
2022-12-30
euro
4.6899
```

#### `getMonthTablesA` method

Returns the `A` table iterator from a specific month. Rates here are grouped into tables,
which represent the actual data structure provided by NBP. To get the rates there needs
to be second iteration:

```php
$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    foreach ($table->getRates() as $rate) {
        echo "{$rate->getCurrencyCode()} rate from table {$table->getNo()} is {$rate->getValue()}\n";
    }
}
```

Outputs:

```
THB rate from table 042/A/NBP/2023 is 0.1267
USD rate from table 042/A/NBP/2023 is 4.4094
AUD rate from table 042/A/NBP/2023 is 2.9819
...
THB rate from table 043/A/NBP/2023 is 0.1266
USD rate from table 043/A/NBP/2023 is 4.4002
AUD rate from table 043/A/NBP/2023 is 2.9638
...
```

Example getting specific rate:
```php
$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    $chfRate = $table->getRate('CHF');
    echo "{$chfRate->getCurrencyCode()} rate from table {$table->getNo()} is {$chfRate->getValue()}\n";
}
```

Outputs:
```
CHF rate from table 042/A/NBP/2023 is 4.7031
CHF rate from table 043/A/NBP/2023 is 4.6743
CHF rate from table 044/A/NBP/2023 is 4.728
CHF rate from table 045/A/NBP/2023 is 4.7402
// ...
```

#### `getMonthTablesB` method

Returns the `B` table iterator from a specific month.

Example:
```php
$bTablesFromMarch = $currencyAverages->getMonthTablesB(2023, 3);

foreach ($bTablesFromMarch as $table) {
    $chfRate = $table->getRate('MNT');
    echo "{$chfRate->getCurrencyName()} rate from table {$table->getNo()} is {$chfRate->getValue()}\n";
}
```

Outputs:
```
tugrik (Mongolia) rate from table 009/B/NBP/2023 is 0.001249
tugrik (Mongolia) rate from table 010/B/NBP/2023 is 0.001262
tugrik (Mongolia) rate from table 011/B/NBP/2023 is 0.001238
tugrik (Mongolia) rate from table 012/B/NBP/2023 is 0.001233
tugrik (Mongolia) rate from table 013/B/NBP/2023 is 0.001225
// ...
```

##### Warning about missing currencies in table B

In table `B` there can be multiple currencies with the same code.

It is also possible, that a specific currency is present in table from one day,
but is not present in table from the next day.

In such case you should not use the `getRate($rate)` method but rather
iterate over all currencies returned by `getRates()`.

### Currency trading rates service

This service is used to get buy and sell currency rates from NBP tables.

#### `fromMonth` method

Returns trading rates from entire month.

Example:

```php
$tradingRatesFromApril = $currencyTrading->fromMonth(2023, 4)->toArray();
foreach ($tradingRatesFromApril as $rate) {
    printf(
        "%s rate from %s effective day traded on %s ask price is %s, bid price is %s\n",
        $rate->getCurrencyCode(),
        $rate->getEffectiveDate()->format('Y-m-d'),
        $rate->getTradingDate()->format('Y-m-d'),
        $rate->getAsk(),
        $rate->getBid()
    );
}
```

```
USD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 4.3338, bid price is 4.248
AUD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 2.9072, bid price is 2.8496
CAD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 3.2033, bid price is 3.1399
EUR rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 4.7208, bid price is 4.6274
...
```

#### `fromEffectiveDay` method

Return rates from effective date.

Example:

```php
$gbpFromApril4th = $currencyTrading->fromEffectiveDay('2023-04-04')->getRate('GBP');
printf(
    '%s rate from %s effective day traded on %s ask price is %s, bid price is %s',
    $gbpFromApril4th->getCurrencyCode(),
    $gbpFromApril4th->getEffectiveDate()->format('Y-m-d'),
    $gbpFromApril4th->getTradingDate()->format('Y-m-d'),
    $gbpFromApril4th->getAsk(),
    $gbpFromApril4th->getBid()
);
```

Outputs:

```
GBP rate from 2023-04-04 effective day traded on 2023-04-03 ask price is 5.3691, bid price is 5.2627
```

#### `fromTradingDay` method

Return rates from trading date.

Example:

```php
$gbpFromApril4th = $currencyTrading->fromTradingDay('2023-04-04')->getRate('GBP');
printf(
    '%s rate from %s effective day traded on %s ask price is %s, bid price is %s',
    $gbpFromApril4th->getCurrencyCode(),
    $gbpFromApril4th->getEffectiveDate()->format('Y-m-d'),
    $gbpFromApril4th->getTradingDate()->format('Y-m-d'),
    $gbpFromApril4th->getAsk(),
    $gbpFromApril4th->getBid()
);
```

Outputs:

```
GBP rate from 2023-04-05 effective day traded on 2023-04-04 ask price is 5.4035, bid price is 5.2965
```

### Gold rates service

This service is used to get gold commodity rates from NBP tables.

#### `fromMonth` method

Gets all rates from specific month.

Example:
```php
$jan2013rates = $goldRates->fromMonth(2013, 1);
foreach ($jan2013rates as $rate) {
    echo "Gold rate from {$rate->getDate()->format('Y-m-d')} is {$rate->getValue()}\n";
}
```

Outputs:

```
Gold rate from 2013-01-02 is 165.83
Gold rate from 2013-01-03 is 166.97
Gold rate from 2013-01-04 is 167.43
...
```

#### `fromDay` method

Returns a gold rate from specific date.

Example:

```php
$goldRateFromJan2nd2014 = $goldRates->fromDay('2014-01-02');
printf(
    'Gold rate from %s is %s',
    $goldRateFromJan2nd2014->getDate()->format('Y-m-d'),
    $goldRateFromJan2nd2014->getValue()
);
```

Outputs:

```
Gold rate from 2014-01-02 is 116.35
```

#### `fromDayBefore` method

Returns a gold rate from before a specific date.

Example:

```php
$goldRateBeforeJan2nd = $goldRates->fromDayBefore('2014-01-02');
printf(
    'Gold rate from %s is %s',
    $goldRateBeforeJan2nd->getDate()->format('Y-m-d'),
    $goldRateBeforeJan2nd->getValue()
);
```

Outputs:

```
Gold rate from 2013-12-31 is 116.89
```

## Using cache

> Note that a library implementing PSR-6 has to be provided in order to use the caching
abilities.

The `CachedTransport` class is a proxy for all other transport implementations.
This transport has to be backed by another transport, as it relies on it to
make the actual requests that have not been cached yet.

### Example

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter as CachePoolAdapter;

// 1) create repository backed by caching transport
$cachePool = new CachePoolAdapter();
$cachingTransportFactory = CachingTransportFactory::new($cachePool);
$client = NbpWebClient::new(transportFactory: $cachingTransportFactory);
$nbpRepository = NbpWebRepository::new($client);

// 2) create needed services using cache-backed repository: 
$currencyAverages = new CurrencyAverageRatesService($nbpRepository);
$currencyTrading = new CurrencyTradingRatesService($nbpRepository);
$goldRates = new GoldRatesService($nbpRepository);
```

### Using different transport

The library uses Symfony HTTP Client and Guzzle as default transports.
If those packages are not available then it falls back to the `file_get_contents`
method. This may not be ideal in some situations. Especially when there is no access
to HTTP client packages as may be the case when using PHP version prior to 8.0.

In such cases 

## Layers

### Service layer

The service consists of [facades](https://refactoring.guru/design-patterns/facade)
for the package. Classes here are named `services` instead of facades due to common
understanding of this word as something through which you access the internals of a system.

This layer provides a high level business-oriented methods for interacting
with the NBP API.
It exposes most common use cases of the `nbp-php` package and is the
likely starting point for all applications using this package.
One needs to interact with other layers only for more complex tasks.

The service layer is structured in the way that it directly communicates 
with the repository layer.

### Repository layer

Repository layer allows getting data from NBP API by providing methods that
closely reflect the NBP Web API. This layer operates on higher level, 
hydrated domain objects.

The only constraint here is that the repository
layer operates on month-based dates. So for example you can get trading rates
from entire month, but in order to retrieve a specific date you have to iterate
through the retrieved month (you can use service layer for that).
This is by design for the purpose of reducing network traffic to the NBP servers.
It also allows simplifying caching on the transport layer, because there is no need
for any cache-pooling logic.

### Client layer

Client layer is a bridge between the repository and the transport layer.
It processes request objects on the input, and then it uses the transport layer 
to fulfill those requests.

The requests thet client layer uses are higher-level requests then those on
the transport layer. They implement `NbpClientRequest` interface.

### Transport layer

The transport layer is responsible for directly interacting with the NBP API.
A few independent transport implementations are provided for serving
connections to the NBP API. 

It is also equipped with a convenient factories which pick the most appropriate
implementation depending on installed libraries and configuration.

[ico-version]:https://img.shields.io/packagist/v/maciej-sz/nbp-php.svg?style=plastic
[ico-travis]:https://img.shields.io/travis/maciej-sz/nbp-php/master.svg?style=plastic
[ico-no-deps]:https://img.shields.io/badge/dependencies-none-brightgreen.svg?style=plastic
[ico-license]:https://img.shields.io/badge/license-MIT-blue.svg?style=plastic

[link-packagist]:https://packagist.org/packages/maciej-sz/nbp-php
[link-travis]:https://travis-ci.org/maciej-sz/nbp-php
[link-license]:https://github.com/maciej-sz/nbp-php/blob/master/LICENSE