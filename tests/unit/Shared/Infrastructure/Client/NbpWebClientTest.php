<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NbpWebClientTest extends TestCase
{
    public function testGetCurrencyAveragesTableA(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/A/2022-01-02/2022-01-03'],
            $webClient->getCurrencyAveragesTableA('2022-01-02', '2022-01-03')
        );
    }

    public function testGetCurrencyAveragesTableB(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/B/2022-01-02/2022-01-03'],
            $webClient->getCurrencyAveragesTableB('2022-01-02', '2022-01-03')
        );
    }

    public function testGetCurrencyTradingTables(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/exchangerates/tables/C/2022-01-02/2022-01-03'],
            $webClient->getCurrencyTradingTables('2022-01-02', '2022-01-03')
        );
    }

    public function testGetGoldRates(): void
    {
        $webClient = new NbpWebClient($this->getTransportMock());
        $this->assertSame(
            ['/api/cenyzlota/2022-01-02/2022-01-03'],
            $webClient->getGoldRates('2022-01-02', '2022-01-03')
        );
    }

    /**
     * @return MockObject&Transport
     */
    private function getTransportMock(): MockObject
    {
        $transportMock = $this->createMock(Transport::class);
        $transportMock
            ->method('request')
            ->willReturnCallback(function(string $path){
                return [$path];
            })
        ;

        return $transportMock;
    }
}
