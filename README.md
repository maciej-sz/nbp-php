# nbp-php

API for accessing Polish National Bank (NBP - Narodowy Bank Polski) currency and commodities exchange rates.

[![Latest Version on Packagist][badge-version]][link-packagist]
[![No dependencies][badge-no-deps]][link-packagist]
[![MIT License][badge-license]][link-license]

[![Tests Coverage][badge-coverage]][link-coverage]<br />
[![PHP 8.1 Test Result][badge-test-result-8.1]][link-php-tests]<br />
[![PHP 8.2 Test Result][badge-test-result-8.2]][link-php-tests]<br />
[![PHP 8.3 Test Result][badge-test-result-8.3]][link-php-tests]<br />
[![PHP 8.4 Test Result][badge-test-result-8.4]][link-php-tests]<br />
[![PHP 8.5 Test Result][badge-test-result-8.5]][link-php-tests]

## Installing via composer

```composer require maciej-sz/nbp-php```


## Usage

The full API of this library can be found in [api.md](api.md).

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

See the [examples](./examples/) directory for complete and runnable examples.



### Using cache

_Note that a library implementing PSR-6 has to be provided in order to use the caching
abilities._

The `CachedTransport` class is a proxy for all other transport implementations.
This transport has to be backed by another transport, as it relies on it to
make the actual requests that have not been cached yet.

#### Example

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

### Using custom transport

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

## Architecture

On the infrastructure level the code utilizes layered architecture to keep the concerns separated.

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

[badge-version]:https://img.shields.io/packagist/v/maciej-sz/nbp-php.svg?style=shield
[badge-no-deps]:https://img.shields.io/badge/dependencies-none-brightgreen.svg?style=shield
[badge-license]:https://img.shields.io/badge/license-MIT-blue.svg?style=shield

[badge-test-result-8.1]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Ftest-result-8.1
[badge-test-result-8.2]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Ftest-result-8.2
[badge-test-result-8.3]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Ftest-result-8.3
[badge-test-result-8.4]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Ftest-result-8.4
[badge-test-result-8.5]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Ftest-result-8.5

[badge-coverage]:https://img.shields.io/endpoint?url=https%3A%2F%2Fkvdb.io%2F96S9JomzUfJKGdkpUQHBkD%2Fcoverage

[link-packagist]:https://packagist.org/packages/maciej-sz/nbp-php
[link-license]:https://github.com/maciej-sz/nbp-php/blob/master/LICENSE
[link-php-tests]:https://github.com/maciej-sz/nbp-php/actions/workflows/php-tests.yml
[link-coverage]:https://github.com/maciej-sz/nbp-php/actions/workflows/master-coverage.yml