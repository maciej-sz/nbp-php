<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Integration;

use donatj\MockWebServer\MockWebServer;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use MaciejSz\Nbp\Test\Fixtures\WebServer\MockWebServerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;

class SymfonyHttpTransportTest extends TestCase
{
    /** @var MockWebServer */
    private static $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = (new MockWebServerFactory())->create();
        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    public function testMakeRequest(): void
    {
        $httpClient = HttpClient::createForBaseUri(self::$server->getServerRoot());
        $transport = new SymfonyHttpTransport($httpClient);
        $result = $transport->get('/api/exchangerates/tables/A/2023-03-01/2023-03-02');

        self::assertCount(2, $result);
        self::assertEquals('042/A/NBP/2023', $result[0]['no']);
        self::assertEquals('043/A/NBP/2023', $result[1]['no']);
    }
}
