<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingTransportTest extends TestCase
{
    public function testFetchHit(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem
            ->expects(self::once())
            ->method('isHit')
            ->willReturn(true)
        ;

        $cacheItem
            ->expects(self::once())
            ->method('get')
            ->willReturn(['foo'])
        ;

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->expects(self::once())
            ->method('getItem')
            ->with(urlencode('/api/foo'))
            ->willReturn($cacheItem)
        ;

        $backend = $this->createMock(Transport::class);

        $transportSut = CachingTransport::new($backend, $cachePool);
        $fetchedItem = $transportSut->get('/api/foo');
        self::assertSame(['foo'], $fetchedItem);
    }

    public function testFetchNoHit(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem
            ->expects(self::once())
            ->method('isHit')
            ->willReturn(false)
        ;

        $cacheItem
            ->expects(self::once())
            ->method('set')
            ->with(['uncached-foo'])
        ;

        $cacheItem
            ->expects(self::once())
            ->method('get')
            ->willReturn(['cached-foo'])
        ;

        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->expects(self::once())
            ->method('getItem')
            ->with(urlencode('/api/foo'))
            ->willReturn($cacheItem)
        ;

        $cachePool
            ->expects(self::once())
            ->method('save')
            ->with($cacheItem)
        ;

        $backend = $this->createMock(Transport::class);
        $backend
            ->expects(self::once())
            ->method('get')
            ->with('/api/foo')
            ->willReturn(['uncached-foo'])
        ;

        $transportSut = CachingTransport::new($backend, $cachePool);
        $fetchedItem = $transportSut->get('/api/foo');
        self::assertSame(['cached-foo'], $fetchedItem);
    }
}
