<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use GuzzleHttp\Client;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\GuzzleTransport;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class GuzzleTransportTest extends TestCase
{
    public function testGet(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body
            ->expects(self::once())
            ->method('getContents')
            ->willReturn('{"foo": 123.45}')
        ;

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($body)
        ;

        $clientMockBuilder = $this->getMockBuilder(Client::class);
        $clientMockBuilder->addMethods(['get']);
        $client = $clientMockBuilder->getMock();
        $client
            ->expects(self::once())
            ->method('get')
            ->willReturn($response)
        ;
        
        $transport = new GuzzleTransport($client);
        self::assertSame(['foo' => 123.45], $transport->get('/'));
    }

    public function testGetInvalidResponse(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body
            ->expects(self::once())
            ->method('getContents')
            ->willReturn('BOGUS')
        ;

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($body)
        ;

        $clientMockBuilder = $this->getMockBuilder(Client::class);
        $clientMockBuilder->onlyMethods(['getConfig'])->addMethods(['get']);
        $client = $clientMockBuilder->getMock();
        $client
            ->expects(self::once())
            ->method('get')
            ->willReturn($response)
        ;

        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects(self::once())
            ->method('__toString')
            ->willReturn('https://www.example.com/')
        ;

        $client
            ->expects(self::once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn($uri)
        ;

        $transport = new GuzzleTransport($client);

        self::expectException(TransportException::class);
        self::expectExceptionMessage('Cannot decode JSON data from https://www.example.com/foo?bar');

        $transport->get('/foo?bar');
    }
}
