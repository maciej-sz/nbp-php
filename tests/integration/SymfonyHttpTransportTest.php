<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Integration;

use donatj\MockWebServer\MockWebServer;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use MaciejSz\Nbp\Test\Fixtures\WebServer\MockWebServerMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class SymfonyHttpTransportTest extends TestCase
{
    /** @var MockWebServer */
    private static $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = (new MockWebServerMother())->create();
        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    public function testMakeRequest(): void
    {
        $server = self::$server;
        $serverRoot = $server->getServerRoot();
        $httpClient = HttpClient::createForBaseUri($serverRoot);
        $transport = new SymfonyHttpTransport($httpClient);
        /** @var array<array{no: string}> $result */
        $result = $transport->get('/api/exchangerates/tables/A/2023-03-01/2023-03-02');

        self::assertCount(2, $result);
        self::assertEquals('042/A/NBP/2023', $result[0]['no']);
        self::assertEquals('043/A/NBP/2023', $result[1]['no']);
    }
}
