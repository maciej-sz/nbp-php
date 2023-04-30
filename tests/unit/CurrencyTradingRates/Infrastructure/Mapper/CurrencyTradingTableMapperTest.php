<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingRatesMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class CurrencyTradingTableMapperTest extends TestCase
{
    public function testRawDataToDomainObject(): void
    {
        $ratesMapperMock = $this->createMock(CurrencyTradingRatesMapper::class);
        $ratesMapperMock
            ->expects(self::once())
            ->method('rawDataToDomainObjectCollection')
            ->willReturn([])
        ;

        $tableMapper = new CurrencyTradingTableMapper($ratesMapperMock);
        $tables = $this->fetchFixtureTables();
        $tableC = $tableMapper->rawDataToDomainObject($tables[0]);

        self::assertSame('C', $tableC->getLetter());
        self::assertSame('042/C/NBP/2023', $tableC->getNo());
        self::assertSame('2023-02-28T00:00:00+01:00', $tableC->getTradingDate()->format('c'));
        self::assertSame('2023-03-01T00:00:00+01:00', $tableC->getEffectiveDate()->format('c'));
        self::assertSame([], $tableC->getRates());
    }

    public function testGetRate(): void
    {
        $tables = $this->fetchFixtureTables();

        $tradingRate = new CurrencyTradingRate(
            'dolar gondorski',
            'GDD',
            12.3,
            12.4,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
        $ratesMapperMock = $this->createMock(CurrencyTradingRatesMapper::class);
        $ratesMapperMock
            ->expects(self::once())
            ->method('rawDataToDomainObjectCollection')
            ->willReturn(['GDD' => $tradingRate])
        ;

        $tableMapper = new CurrencyTradingTableMapper($ratesMapperMock);

        $tableC = $tableMapper->rawDataToDomainObject($tables[0]);
        $this->assertSame($tradingRate, $tableC->getRate('GDD'));
    }

    /**
     * @return array<mixed>
     */
    private function fetchFixtureTables(): array
    {
        $fixturesRepository = new FixturesRepository();

        return $fixturesRepository->fetchArray('/api/exchangerates/tables/C/2023-03-01/2023-03-02/data');
    }
}
