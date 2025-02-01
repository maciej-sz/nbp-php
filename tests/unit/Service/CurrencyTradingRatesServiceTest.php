<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Service;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\Service\CurrencyTradingRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRatesServiceTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        $instance = CurrencyTradingRatesService::create();
        self::assertInstanceOf(CurrencyTradingRatesService::class, $instance);
    }

    public function testGetMonthsTable(): void
    {
        $table = $this->createStub(CurrencyTradingTable::class);

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyTradingTables')
            ->with(2023, 4)
            ->willReturn([$table])
        ;
        $service = new CurrencyTradingRatesService($repository);

        $tablesIterable = $service->getMonthTables(2023, 4);
        $tables = is_array($tablesIterable)
            ? $tablesIterable
            : iterator_to_array($tablesIterable);

        self::assertSame([$table], $tables);
    }

    public function testFromMonth(): void
    {
        $rate = $this->createStub(CurrencyTradingRate::class);

        $table = $this->createMock(CurrencyTradingTable::class);
        $table
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rate])
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyTradingTables')
            ->with(2023, 4)
            ->willReturn([$table])
        ;

        $service = new CurrencyTradingRatesService($repository);
        $rates = $service->fromMonth(2023, 4)->toArray();

        self::assertSame([$rate], $rates);
    }

    public function testFromEffectiveDay(): void
    {
        $table1 = $this->createMock(CurrencyTradingTable::class);
        $table1
            ->expects(self::once())
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-14'))
        ;

        $table2 = $this->createMock(CurrencyTradingTable::class);
        $table2
            ->expects(self::once())
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-15'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyTradingTables')
            ->with(2023, 4)
            ->willReturn([$table1, $table2])
        ;

        $service = new CurrencyTradingRatesService($repository);
        self::assertSame($table2, $service->fromEffectiveDay('2023-04-15'));
    }

    public function testFromEffectiveDayNotFound(): void
    {
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyTradingTables')
            ->with(2023, 4)
            ->willReturn([])
        ;

        $service = new CurrencyTradingRatesService($repository);

        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Rates table for effective date 2023-04-01 is not available');

        $service->fromEffectiveDay('2023-04-01');
    }

    public function testFromTradingDay(): void
    {
        $table1 = $this->createMock(CurrencyTradingTable::class);
        $table1
            ->expects(self::once())
            ->method('getTradingDate')
            ->willReturn(new \DateTimeImmutable('2023-04-14'))
        ;

        $table2 = $this->createMock(CurrencyTradingTable::class);
        $table2
            ->expects(self::once())
            ->method('getTradingDate')
            ->willReturn(new \DateTimeImmutable('2023-04-15'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyTradingTables')
            ->with(2023, 4)
            ->willReturn([$table1, $table2])
        ;

        $service = new CurrencyTradingRatesService($repository);
        self::assertSame($table2, $service->fromTradingDay('2023-04-15'));
    }

    public function testFromTradingDayNextMonth(): void
    {
        $table1 = $this->createMock(CurrencyTradingTable::class);
        $table1
            ->expects(self::once())
            ->method('getTradingDate')
            ->willReturn(new \DateTimeImmutable('2022-12-30'))
        ;

        $table2 = $this->createMock(CurrencyTradingTable::class);
        $table2
            ->expects(self::once())
            ->method('getTradingDate')
            ->willReturn(new \DateTimeImmutable('2022-12-31'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getCurrencyTradingTables')
            ->willReturnMap([
                [2022, 12, [$table1]],
                [2023, 1, [$table2]],
            ])
        ;

        $service = new CurrencyTradingRatesService($repository);
        self::assertSame($table2, $service->fromTradingDay('2022-12-31'));
    }

    public function testFromTradingDayNotFound(): void
    {
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getCurrencyTradingTables')
            ->willReturnMap([
                [2022, 12, []],
                [2023, 1, []],
            ])
        ;

        $service = new CurrencyTradingRatesService($repository);

        self::expectExceptionMessage(InvalidDateException::class);
        self::expectExceptionMessage('Rates table for trading date 2022-12-31 is not available');

        $service->fromTradingDay('2022-12-31');
    }
}
