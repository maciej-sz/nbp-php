<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Functional\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\DefaultTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\GuzzleTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use PHPUnit\Framework\TestCase;

class DefaultTransportFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new DefaultTransportFactory();
        $transport = $factory->create('https://dummy.restapiexample.com');
        self::assertInstanceOf(Transport::class, $transport);
    }

    public function testTryCreateSymfonyTransport(): void
    {
        $factory = new DefaultTransportFactory();
        $transport = $factory->tryCreateSymfonyTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(SymfonyHttpTransport::class, $transport);
    }

    public function testTryCreateGuzzleTransport(): void
    {
        $factory = new DefaultTransportFactory();
        $transport = $factory->tryCreateGuzzleTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(GuzzleTransport::class, $transport);
    }

    public function testCreateFallbackTransport(): void
    {
        $factory = new DefaultTransportFactory();
        $transport = $factory->createFallbackTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(FileContentsTransport::class, $transport);
    }
}
