<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyHttpTransportTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        self::expectNotToPerformAssertions();
        new SymfonyHttpTransport();
    }

    public function testGet(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('toArray')
            ->willReturn(['foo'])
        ;

        $client = $this->createMock(HttpClientInterface::class);
        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response)
        ;

        $transport = new SymfonyHttpTransport($client);
        self::assertSame(['foo'], $transport->get('/'));
    }
}
