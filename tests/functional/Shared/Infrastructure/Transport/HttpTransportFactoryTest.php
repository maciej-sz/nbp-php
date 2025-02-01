<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Functional\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\GuzzleTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\HttpTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use PHPUnit\Framework\TestCase;

class HttpTransportFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new HttpTransportFactory();
        $transport = $factory->make('https://dummy.restapiexample.com');
        self::assertInstanceOf(Transport::class, $transport);
    }

    /**
     * @requires PHP >= 8.1
     */
    public function testTryCreateSymfonyTransport(): void
    {
        $factory = new HttpTransportFactory();
        $transport = $factory->tryCreateSymfonyTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(SymfonyHttpTransport::class, $transport);
    }

    public function testTryCreateGuzzleTransport(): void
    {
        $factory = new HttpTransportFactory();
        $transport = $factory->tryCreateGuzzleTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(GuzzleTransport::class, $transport);
    }

    public function testCreateFallbackTransport(): void
    {
        $factory = new HttpTransportFactory();
        $transport = $factory->createFallbackTransport('https://dummy.restapiexample.com');
        self::assertInstanceOf(FileContentsTransport::class, $transport);
    }
}
