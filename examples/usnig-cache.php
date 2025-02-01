<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransportFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter as CachePoolAdapter;

// 1) create repository backed by caching transport
$cachePool = new CachePoolAdapter();
$cachingTransportFactory = CachingTransportFactory::create($cachePool);
$client = NbpWebClient::create(transportFactory: $cachingTransportFactory);
$nbpRepository = NbpWebRepository::create($client);

// 2) create needed services using cache-backed repository:
$goldRates = new GoldRatesService($nbpRepository);

// 3) run multiple times to check the effect of caching:
$start = microtime(true);
$goldRates->fromDayBefore('2013-05-15')->getValue();
$end = microtime(true);
$took = $end - $start;
printf('Getting the rate took %F ms', $took * 1000);
