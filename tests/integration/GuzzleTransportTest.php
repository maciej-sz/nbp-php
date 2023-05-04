<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Integration;

use donatj\MockWebServer\MockWebServer;
use GuzzleHttp\Client;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\GuzzleTransport;
use MaciejSz\Nbp\Test\Fixtures\WebServer\MockWebServerFactory;
use PHPUnit\Framework\TestCase;

class GuzzleTransportTest extends TestCase
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
        $guzzleClient = new Client([
            'base_uri' => self::$server->getServerRoot(),
        ]);
        $transport = new GuzzleTransport($guzzleClient);
        /** @var array<array{no: string}> $result */
        $result = $transport->get('/api/exchangerates/tables/A/2023-03-01/2023-03-02');

        self::assertCount(2, $result);
        self::assertEquals('042/A/NBP/2023', $result[0]['no']);
        self::assertEquals('043/A/NBP/2023', $result[1]['no']);
    }

    public function testInvalidJson(): void
    {
        self::expectException(TransportException::class);
        self::expectExceptionMessageMatches('/^Cannot decode JSON data from.*\/api\/bogus$/');

        $guzzleClient = new Client([
            'base_uri' => self::$server->getServerRoot(),
        ]);
        $transport = new GuzzleTransport($guzzleClient);
        $transport->get('/api/bogus');
    }
}
