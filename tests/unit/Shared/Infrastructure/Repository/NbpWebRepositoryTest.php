<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Repository;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableARequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableBRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyTradingTableRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\GoldRatesRequest;
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

        $currencyAverageTablMock01 = $this->createMock(CurrencyAveragesTable::class);
        $currencyAverageTablMock02 = $this->createMock(CurrencyAveragesTable::class);

        $averageTableMapperMock = $this->createMock(CurrencyAveragesTableMapper::class);
        $averageTableMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn($currencyAverageTablMock01, $currencyAverageTablMock02)
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

        $this->assertSame($currencyAverageTablMock01, $tables[0]);
        $this->assertSame($currencyAverageTablMock02, $tables[1]);
    }

    public function testGetCurrencyAveragesTableB(): void
    {
        $clientMock = $this->createMock(NbpClient::class);
        $clientMock
            ->expects(self::once())
            ->method('send')
            ->with(new CurrencyAveragesTableBRequest('2023-03-01', '2023-03-31'))
            ->willReturn([[], []])
        ;

        $currencyAverageTablMock01 = $this->createMock(CurrencyAveragesTable::class);
        $currencyAverageTablMock02 = $this->createMock(CurrencyAveragesTable::class);

        $averageTableMapperMock = $this->createMock(CurrencyAveragesTableMapper::class);
        $averageTableMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn($currencyAverageTablMock01, $currencyAverageTablMock02)
        ;

        $repository = new NbpWebRepository(
            $clientMock,
            null,
            $averageTableMapperMock,
            $this->createMock(CurrencyTradingTableMapper::class),
            $this->createMock(GoldRatesMapper::class)
        );

        $tablesIterable = $repository->getCurrencyAveragesTableB(2023, 3);
        $tables = is_array($tablesIterable) ? $tablesIterable : iterator_to_array($tablesIterable);

        $this->assertSame($currencyAverageTablMock01, $tables[0]);
        $this->assertSame($currencyAverageTablMock02, $tables[1]);
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

        $currencyTradingTableMock01 = $this->createMock(CurrencyTradingTable::class);
        $currencyTradingTableMock02 = $this->createMock(CurrencyTradingTable::class);

        $tradingTableMapperMock = $this->createMock(CurrencyTradingTableMapper::class);
        $tradingTableMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn($currencyTradingTableMock01, $currencyTradingTableMock02)
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

        $this->assertSame($currencyTradingTableMock01, $tables[0]);
        $this->assertSame($currencyTradingTableMock02, $tables[1]);
    }

    public function testGetGoldRates(): void
    {
        $clientMock = $this->createMock(NbpClient::class);
        $clientMock
            ->expects(self::once())
            ->method('send')
            ->with(new GoldRatesRequest('2023-03-01', '2023-03-31'))
            ->willReturn([[], []])
        ;

        $currencyTradingTableMock01 = $this->createMock(GoldRate::class);
        $currencyTradingTableMock02 = $this->createMock(GoldRate::class);

        $goldRatesMapperMock = $this->createMock(GoldRatesMapper::class);
        $goldRatesMapperMock
            ->expects(self::exactly(2))
            ->method('rawDataToDomainObject')
            ->willReturn([$currencyTradingTableMock01], [$currencyTradingTableMock02])
        ;

        $repository = new NbpWebRepository(
            $clientMock,
            null,
            $this->createMock(CurrencyAveragesTableMapper::class),
            $this->createMock(CurrencyTradingTableMapper::class),
            $goldRatesMapperMock
        );

        $ratesIterable = $repository->getGoldRates(2023, 3);
        $rates = is_array($ratesIterable) ? $ratesIterable : iterator_to_array($ratesIterable);

        $this->assertSame($currencyTradingTableMock01, $rates[0][0]);
        $this->assertSame($currencyTradingTableMock02, $rates[1][0]);
    }
}
