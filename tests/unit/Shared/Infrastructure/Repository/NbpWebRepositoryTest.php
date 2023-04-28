<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Repository;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableARequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyTradingTableRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use PHPUnit\Framework\TestCase;

class NbpWebRepositoryTest extends TestCase
{
    public function testGetCurrencyAveragesTableA(): void
    {
        $clientMock = $this->createMock(NbpClient::class);
        $clientMock
            ->expects(self::once())
            ->method('send')
            ->with(new CurrencyAveragesTableARequest('2023-03-01', '2023-03-31'))
            ->willReturn([[], []])
        ;

        $currencyAverageTablMockA = $this->createMock(CurrencyAveragesTable::class);
        $currencyAverageTablMockB = $this->createMock(CurrencyAveragesTable::class);

        $averageTableMapperMock = $this->createMock(CurrencyAveragesTableMapper::class);
        $averageTableMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn($currencyAverageTablMockA, $currencyAverageTablMockB)
        ;

        $repository = new NbpWebRepository(
            $clientMock,
            null,
            $averageTableMapperMock,
            $this->createMock(CurrencyTradingTableMapper::class),
            $this->createMock(GoldRatesMapper::class)
        );

        $tablesIterable = $repository->getCurrencyAveragesTableA(2023, 3);
        $tables = is_array($tablesIterable) ? $tablesIterable : iterator_to_array($tablesIterable);

        $this->assertSame($currencyAverageTablMockA, $tables[0]);
        $this->assertSame($currencyAverageTablMockB, $tables[1]);
    }

    public function testGetCurrencyTradingTables(): void
    {
        $clientMock = $this->createMock(NbpClient::class);
        $clientMock
            ->expects(self::once())
            ->method('send')
            ->with(new CurrencyTradingTableRequest('2023-03-01', '2023-03-31'))
            ->willReturn([[], []])
        ;

        $currencyTradingTableMockA = $this->createMock(CurrencyTradingTable::class);
        $currencyTradingTableMockB = $this->createMock(CurrencyTradingTable::class);

        $tradingTableMapperMock = $this->createMock(CurrencyTradingTableMapper::class);
        $tradingTableMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn($currencyTradingTableMockA, $currencyTradingTableMockB)
        ;

        $repository = new NbpWebRepository(
            $clientMock,
            null,
            $this->createMock(CurrencyAveragesTableMapper::class),
            $tradingTableMapperMock,
            $this->createMock(GoldRatesMapper::class)
        );

        $tablesIterable = $repository->getCurrencyTradingTables(2023, 3);
        $tables = is_array($tablesIterable) ? $tablesIterable : iterator_to_array($tablesIterable);

        $this->assertSame($currencyTradingTableMockA, $tables[0]);
        $this->assertSame($currencyTradingTableMockB, $tables[1]);
    }
}
