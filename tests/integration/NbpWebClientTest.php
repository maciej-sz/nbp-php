<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Integration;

use donatj\MockWebServer\MockWebServer;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableARequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;
use MaciejSz\Nbp\Test\Fixtures\WebServer\MockWebServerFactory;
use PHPUnit\Framework\TestCase;

class NbpWebClientTest extends TestCase
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

    public function testCurrencyTablesUsingTransportFactory(): void
    {
        $factory = new class() implements TransportFactory {
            public function create(string $baseUri = NbpWebClient::BASE_URL): Transport
            {
                return new FileContentsTransport($baseUri);
            }
        };

        $client = NbpWebClient::create(self::$server->getServerRoot(), $factory);
        /** @var array<array{no: string}> $result */
        $result = $client->send(new CurrencyAveragesTableARequest('2023-03-01', '2023-03-02'));

        self::assertCount(2, $result);
        self::assertEquals('042/A/NBP/2023', $result[0]['no']);
        self::assertEquals('043/A/NBP/2023', $result[1]['no']);
    }
}
