<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\CachingTransportFactory;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class CachingTransportFactoryTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        $cachePool = $this->createStub(CacheItemPoolInterface::class);
        $factory = CachingTransportFactory::create($cachePool);
        self::assertInstanceOf(CachingTransportFactory::class, $factory);
    }

    public function testCreate(): void
    {
        $cachePool = $this->createStub(CacheItemPoolInterface::class);
        $factory = CachingTransportFactory::create($cachePool);
        $transport = $factory->make('https://example.com');
        self::assertInstanceOf(CachingTransport::class, $transport);
    }
}
