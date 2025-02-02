# nbp-php

API for accessing Polish National Bank (NBP - Narodowy Bank Polski) currency and commodities exchange rates.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-circleci]][link-circleci]
[![Build Status][ico-coverage]][link-coverage]
[![No dependencies][ico-no-deps]][link-packagist]
[![MIT License][ico-license]][link-license]



## Usage

### Installing via composer

```composer require maciej-sz/nbp-php```

### Minimal setup

```php
<?php
require_once 'vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::new();
$rate = $currencyAverages->fromDay('2023-01-02')->fromTable('A')->getRate('USD');

printf('%s rate is %d', $rate->getCurrencyCode(), $rate->getValue());
```

```
USD rate is 4.381100
```

### Examples

All working examples from this README are included in the `examples/` directory of the repository. 

## Services

### CurrencyAverageRatesService

This service provides API for accessing average rates published in NBP tables.

#### `fromMonth` method

Returns flat collection of rates from all NBP tables at given month

```php
$averageRatesFromJanuary = $currencyAverages->fromMonth(2023, 1);

foreach ($averageRatesFromJanuary as $rate) {
    printf(
        '%s rate from %s is %F' . PHP_EOL,
        $rate->getCurrencyCode(),
        $rate->getEffectiveDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
```

```
THB rate from 2023-01-02 is 0.126700
USD rate from 2023-01-02 is 4.381100
AUD rate from 2023-01-02 is 2.976700
...
```

#### `fromDay` method

Returns a dictionary with NBP tables from given day.

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
    ->getRate('EUR')
;

printf(
    '%s rate from %s is %F',
    $eurRateFromBeforeJanuary2nd->getCurrencyCode(),
    $eurRateFromBeforeJanuary2nd->getEffectiveDate()->format('Y-m-d'),
    $eurRateFromBeforeJanuary2nd->getValue()
);
```
```
EUR rate from 2022-12-30 is 4.689900
```

#### `getMonthTablesA` method

Returns the `A` table iterator from a specific month. Rates here are grouped into tables,
which represent the actual data structure provided by NBP. To get the rates there needs
to be second iteration:

```php
$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    foreach ($table->getRates() as $rate) {
        printf(
            '%s rate from table %s is %F' . PHP_EOL,
            $rate->getCurrencyCode(),
            $table->getNo(),
            $rate->getValue()
        );
    }
}
```

```
THB rate from table 042/A/NBP/2023 is 0.126700
USD rate from table 042/A/NBP/2023 is 4.409400
AUD rate from table 042/A/NBP/2023 is 2.981900
...
THB rate from table 043/A/NBP/2023 is 0.126600
USD rate from table 043/A/NBP/2023 is 4.400200
AUD rate from table 043/A/NBP/2023 is 2.963800
...
```

Example getting specific rate:
```php
$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    $chfRate = $table->getRate('CHF');
    printf(
        '%s rate from table %s is %F' . PHP_EOL,
        $chfRate->getCurrencyCode(),
        $table->getNo(),
        $chfRate->getValue()
    );
}
```

```
CHF rate from table 042/A/NBP/2023 is 4.703100
CHF rate from table 043/A/NBP/2023 is 4.674300
CHF rate from table 044/A/NBP/2023 is 4.728000
// ...
```

#### `getMonthTablesB` method

Returns the `B` table iterator from a specific month.

```php
$bTablesFromMarch = $currencyAverages->getMonthTablesB(2022, 3);

foreach ($bTablesFromMarch as $table) {
    try {
        $rate = $table->getRate('MNT');
    } catch (CurrencyCodeNotFoundException $e) {
        continue;
    }
    printf(
        '%s rate from table %s is %F' . PHP_EOL,
        $rate->getCurrencyName(),
        $table->getNo(),
        $rate->getValue()
    );
}
```

```
tugrik (Mongolia) rate from table 009/B/NBP/2022 is 0.001500
tugrik (Mongolia) rate from table 010/B/NBP/2022 is 0.001529
tugrik (Mongolia) rate from table 011/B/NBP/2022 is 0.001469
tugrik (Mongolia) rate from table 012/B/NBP/2022 is 0.001457
tugrik (Mongolia) rate from table 013/B/NBP/2022 is 0.001417
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

```php
$tradingRatesFromApril = $currencyTrading->fromMonth(2023, 4);

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

```
GBP rate from 2023-04-04 effective day traded on 2023-04-03 ask price is 5.3691, bid price is 5.2627
```

#### `fromTradingDay` method

Return rates from trading date.

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

```
GBP rate from 2023-04-05 effective day traded on 2023-04-04 ask price is 5.4035, bid price is 5.2965
```

### Gold rates service

This service is used to get gold commodity rates from NBP tables.

#### `fromMonth` method

Gets all rates from specific month.

```php
$jan2013rates = $goldRates->fromMonth(2013, 1);

foreach ($jan2013rates as $rate) {
    printf(
        'Gold rate from %s is %F' . PHP_EOL,
        $rate->getDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
```

```
Gold rate from 2013-01-02 is 165.830000
Gold rate from 2013-01-03 is 166.970000
Gold rate from 2013-01-04 is 167.430000
...
```

#### `fromDay` method

Returns a gold rate from specific date.

```php
$goldRateFromJan2nd2014 = $goldRates->fromDay('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateFromJan2nd2014->getDate()->format('Y-m-d'),
    $goldRateFromJan2nd2014->getValue()
);
```

```
Gold rate from 2014-01-02 is 116.350000
```

#### `fromDayBefore` method

Returns a gold rate from before a specific date.

```php
$goldRateBeforeJan2nd = $goldRates->fromDayBefore('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateBeforeJan2nd->getDate()->format('Y-m-d'),
    $goldRateBeforeJan2nd->getValue()
);

```

```
Gold rate from 2013-12-31 is 116.890000
```

## Using cache

_Note that a library implementing PSR-6 has to be provided in order to use the caching
abilities._

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
$goldRates = new GoldRatesService($nbpRepository);

// 3) run multiple times to check the effect of caching:
$start = microtime(true);
$goldRates->fromDayBefore('2013-05-15')->getValue();
$end = microtime(true);
$took = $end - $start;
printf('Getting the rate took %F ms', $took * 1000);
```

## Using custom transport

The library uses Symfony HTTP Client and Guzzle as default transports.
If those packages are not available then it falls back to the `file_get_contents`
method. This may not be ideal in some situations. Especially when there is no access
to HTTP client packages as may be the case when using PHP version prior to 8.0.

In such cases it is suggested to use different transport. It can be achieved 
by replacing the `TransportFactory` of the `NbpClient` with your own implementation.

```php
$customTransportFactory = new class() implements TransportFactory {
    public function create(string $baseUri): Transport
    {
        return new class() implements Transport {
            public function get(string $path): array
            {
                echo "Requesting resource: {$path}" . PHP_EOL;

                $ch = curl_init();
                $url = NbpWebClient::BASE_URL . $path;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                try {
                    $output = curl_exec($ch);
                } finally {
                    curl_close($ch);
                }

                echo 'Request successful' . PHP_EOL;

                return json_decode($output, true);
            }
        };
    }
};

$client = NbpWebClient::new(transportFactory: $customTransportFactory);
$nbpRepository = NbpWebRepository::new($client);
$goldRates = GoldRatesService::new($nbpRepository);

$rate = $goldRates->fromDay('2022-01-03');

printf(
    'Gold rate from %s is %F',
    $rate->getDate()->format('Y-m-d'),
    $rate->getValue()
);
```

```
Requesting resource: /api/cenyzlota/2022-01-01/2022-01-31
Request successful
Gold rate from 2022-01-03 is 235.720000
```

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

[ico-version]:https://img.shields.io/packagist/v/maciej-sz/nbp-php.svg?style=shield
[ico-circleci]:https://circleci.com/gh/maciej-sz/nbp-php/tree/master.svg?style=shield
[ico-coverage]:https://img.shields.io/badge/coverage-99%25-brightgreen
[ico-no-deps]:https://img.shields.io/badge/dependencies-none-brightgreen.svg?style=shield
[ico-license]:https://img.shields.io/badge/license-MIT-blue.svg?style=shield

[link-packagist]:https://packagist.org/packages/maciej-sz/nbp-php
[link-circleci]:https://circleci.com/gh/maciej-sz/nbp-php/
[link-coverage]:https://output.circle-artifacts.com/output/job/3c10e60b-e05d-4a55-808e-12cf4aded9bd/artifacts/0/var/coverage/html-report/index.html
[link-license]:https://github.com/maciej-sz/nbp-php/blob/master/LICENSE