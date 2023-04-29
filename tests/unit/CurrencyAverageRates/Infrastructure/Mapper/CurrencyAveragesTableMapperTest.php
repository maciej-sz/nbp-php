<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAverageRatesMapper;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class CurrencyAveragesTableMapperTest extends TestCase
{
    public function testRawDataToDomainObject(): void
    {
        $ratesMapperMock = $this->createMock(CurrencyAverageRatesMapper::class);
        $ratesMapperMock
            ->expects(self::once())
            ->method('rawDataToDomainObjectCollection')
            ->willReturn([])
        ;

        $tableMapper = new CurrencyAveragesTableMapper($ratesMapperMock);

        $fixturesRepository = new FixturesRepository();
        $tableData = $fixturesRepository->fetchArray('/api/exchangerates/tables/A/2023-03-01/2023-03-02/data');

        $tableA = $tableMapper->rawDataToDomainObject($tableData[0]);
        self::assertSame('A', $tableA->getLetter());
        self::assertSame('042/A/NBP/2023', $tableA->getNumber());
        self::assertSame('2023-03-01T00:00:00+01:00', $tableA->getEffectiveDate()->format('c'));
        self::assertSame([], $tableA->getRates());
    }
}
