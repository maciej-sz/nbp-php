<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Integration;

use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachingIntegrationTest extends TestCase
{
    public function testCaching(): void
    {
        $uncachedRates = [
            ['data' => '2023-03-04', 'cena' => 123.4],
        ];
        $cachedRates = [
            ['data' => '2023-03-04', 'cena' => 567.8],
        ];

        $backendTransport = $this->createMock(Transport::class);
        $backendTransport
            ->expects(self::once())
            ->method('get')
            ->willReturn($uncachedRates)
        ;

        $backendFactory = $this->createStub(TransportFactory::class);
        $backendFactory->method('make')->willReturn($backendTransport);

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem
            ->expects(self::exactly(2))
            ->method('isHit')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $cacheItem
            ->expects(self::once())
            ->method('set')
            ->with($uncachedRates)
        ;

        $cacheItem
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturn($cachedRates)
        ;

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->expects(self::exactly(2))
            ->method('getItem')
            ->with(urlencode('/api/cenyzlota/2023-03-01/2023-03-31'))
            ->willReturn($cacheItem)
        ;

        $cachePool
            ->expects(self::once())
            ->method('save')
            ->with($cacheItem)
        ;

        $cachingTransportFactory = CachingTransportFactory::create(
            $cachePool,
            null,
            $backendFactory
        );
        $client = NbpWebClient::create(null, $cachingTransportFactory);
        $nbpRepository = NbpWebRepository::create($client);
        $service = GoldRatesService::create($nbpRepository);

        self::assertSame(567.8, $service->fromDay('2023-03-04')->getValue());
        self::assertSame(567.8, $service->fromDay('2023-03-04')->getValue());
    }

    public function testWithConcreteCachingImplementation(): void
    {
        $mockRates = [
            ['data' => '2023-03-04', 'cena' => 123.4],
        ];

        $cachePool = new ArrayAdapter();

        $backendTransport = $this->createMock(Transport::class);
        $backendTransport
            ->expects(self::once())
            ->method('get')
            ->with('/api/cenyzlota/2023-03-01/2023-03-31')
            ->willReturn($mockRates)
        ;

        $backendFactory = $this->createStub(TransportFactory::class);
        $backendFactory->method('create')->willReturn($backendTransport);

        $cachingTransportFactory = CachingTransportFactory::new(
            $cachePool,
            null,
            $backendFactory
        );
        $client = NbpWebClient::new(null, $cachingTransportFactory);
        $nbpRepository = NbpWebRepository::new($client);
        $service = GoldRatesService::new($nbpRepository);

        self::assertSame(123.4, $service->fromDay('2023-03-04')->getValue());
        self::assertSame(123.4, $service->fromDay('2023-03-04')->getValue());
    }
}
