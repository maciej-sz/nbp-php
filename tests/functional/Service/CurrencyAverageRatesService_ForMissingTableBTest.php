<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Functional\Service;

use donatj\MockWebServer\MockWebServer;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\NoDataException;
use MaciejSz\Nbp\Shared\Domain\Exception\TableNotFoundException;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;
use MaciejSz\Nbp\Test\Fixtures\WebServer\MockWebServerMother;
use PHPUnit\Framework\TestCase;

class CurrencyAverageRatesService_ForMissingTableBTest extends TestCase
{
    /** @var MockWebServer */
    private static $server;

    /** @var CurrencyAverageRatesService CurrencyAverageRatesService */
    private static $currencyRatesSvc;

    public static function setUpBeforeClass(): void
    {
        self::$server = (new MockWebServerMother())->create202408WithOnlyTableA();
        self::$server->start();

        $transportFactory = new class() implements TransportFactory {
            public function create(string $baseUri): Transport
            {
                return new FileContentsTransport($baseUri);
            }
        };

        $client = NbpWebClient::new(self::$server->getServerRoot(), $transportFactory);
        $repository = NbpWebRepository::new($client);
        self::$currencyRatesSvc = CurrencyAverageRatesService::new($repository);
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    public function testFromDayFromTableAWithMissingTableB(): void
    {
        $usdRate = self::$currencyRatesSvc->fromDay('2023-03-02')->fromTable('A')->getRate('USD');

        $this->assertSame('USD', $usdRate->getCurrencyCode());
        $this->assertSame(4.5, $usdRate->getValue());
        $this->assertSame('2023-03-02', $usdRate->getEffectiveDate()->format('Y-m-d'));
    }

    public function testFromDayFromTableBWithMissingTableB(): void
    {
        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'B\' was not found');

        self::$currencyRatesSvc->fromDay('2023-03-02')->fromTable('B');
    }

    public function testFromDayFromTableBWithMissingTableBMissingTable(): void
    {
        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'B\' was not found');

        self::$currencyRatesSvc->fromDay('2023-03-02')->fromTable('B');
    }

    public function testFromDayBeforeFromTableAWithMissingTableB(): void
    {
        $usdRate = self::$currencyRatesSvc->fromDayBefore('2023-03-03')->fromTable('A')->getRate('USD');

        $this->assertSame('USD', $usdRate->getCurrencyCode());
        $this->assertSame(4.5, $usdRate->getValue());
        $this->assertSame('2023-03-02', $usdRate->getEffectiveDate()->format('Y-m-d'));
    }
}
