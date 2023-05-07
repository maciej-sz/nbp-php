<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\E2e\Service;

use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class GoldRatesServiceWithCachingTest extends TestCase
{
    public function testGoldRatesWithCaching(): void
    {
        $httpTransport = FileContentsTransport::new();
        $proxyTransport = $this->createMock(Transport::class);
        $proxyTransport
            ->expects(self::once())
            ->method('get')
            ->with('/api/cenyzlota/2023-03-01/2023-03-31')
            ->willReturnCallback(function (string $path) use ($httpTransport) {
                return $httpTransport->get($path);
            })
        ;

        $proxyTransportFactory = $this->createStub(TransportFactory::class);
        $proxyTransportFactory->method('create')->willReturn($proxyTransport);

        $cacheRegistry = new \ArrayObject();
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem
            ->expects(self::exactly(2))
            ->method('isHit')
            ->willReturnOnConsecutiveCalls(false, true)
        ;
        $cacheItem
            ->expects(self::once())
            ->method('set')
            ->willReturnCallback(
                function (array $data) use ($cacheRegistry, $cacheItem): CacheItemInterface {
                    $cacheRegistry->offsetSet('data', $data);

                    return $cacheItem;
                }
            )
        ;
        $cacheItem
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturnCallback(function () use ($cacheRegistry) {
                return $cacheRegistry->offsetGet('data');
            })
        ;

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->expects(self::exactly(2))
            ->method('getItem')
            ->with('/api/cenyzlota/2023-03-01/2023-03-31')
            ->willReturn($cacheItem)
        ;
        $cachePool
            ->expects(self::once())
            ->method('save')
            ->with($cacheItem)
        ;

        $cachingTransportFactory = CachingTransportFactory::new(
            $cachePool,
            new \DateInterval('PT1S'),
            $proxyTransportFactory
        );
        $client = NbpWebClient::new(null, $cachingTransportFactory);
        $nbpRepository = NbpWebRepository::new($client);
        $goldRates = GoldRatesService::new($nbpRepository);

        self::assertSame(259.79, $goldRates->fromDay('2023-03-03')->getValue());
        self::assertSame(259.79, $goldRates->fromDay('2023-03-03')->getValue());
    }
}
