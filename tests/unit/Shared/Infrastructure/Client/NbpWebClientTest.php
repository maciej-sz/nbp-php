<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableARequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableBRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyTradingTableRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\GoldRatesRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NbpWebClientTest extends TestCase
{
    public function testGetCurrencyAveragesTableA(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/A/2022-03-02/2022-03-02'],
            $webClient->send(new CurrencyAveragesTableARequest('2022-03-02', '2022-03-02'))
        );
    }

    public function testGetCurrencyAveragesTableB(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/B/2022-03-02/2022-03-02'],
            $webClient->send(new CurrencyAveragesTableBRequest('2022-03-02', '2022-03-02'))
        );
    }

    public function testGetCurrencyTradingTables(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/C/2022-03-02/2022-03-02'],
            $webClient->send(new CurrencyTradingTableRequest('2022-03-02', '2022-03-02'))
        );
    }

    public function testGetGoldRates(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/cenyzlota/2022-03-02/2022-03-02'],
            $webClient->send(new GoldRatesRequest('2022-03-02', '2022-03-02'))
        );
    }

    /**
     * @return MockObject&Transport
     */
    private function getTransportMock(): MockObject
    {
        $transportMock = $this->createMock(Transport::class);
        $transportMock
            ->method('get')
            ->willReturnCallback(function (string $path) {
                return [$path];
            })
        ;

        return $transportMock;
    }
}
