<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Service;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\TableNotFoundException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use PHPUnit\Framework\TestCase;

class CurrencyAverageRatesServiceTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        $instance = CurrencyAverageRatesService::new();
        self::assertInstanceOf(CurrencyAverageRatesService::class, $instance);
    }

    public function testMonthTablesA(): void
    {
        $table = $this->createStub(CurrencyAveragesTable::class);

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyAveragesTableA')
            ->with(2023, 4)
            ->willReturn([$table])
        ;

        $service = new CurrencyAverageRatesService($repository);
        $ratesIterable = $service->getMonthTablesA(2023, 4);
        $rates = is_array($ratesIterable)
            ? $ratesIterable
            : iterator_to_array($ratesIterable);

        self::assertSame([$table], $rates);
    }

    public function testMonthTablesB(): void
    {
        $table = $this->createStub(CurrencyAveragesTable::class);

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyAveragesTableB')
            ->with(2023, 4)
            ->willReturn([$table])
        ;

        $service = new CurrencyAverageRatesService($repository);
        $ratesIterable = $service->getMonthTablesB(2023, 4);
        $rates = is_array($ratesIterable)
            ? $ratesIterable
            : iterator_to_array($ratesIterable);

        self::assertSame([$table], $rates);
    }

    public function testFromMonth(): void
    {
        $rateA1 = $this->createStub(CurrencyAverageRate::class);
        $rateA2 = $this->createStub(CurrencyAverageRate::class);
        $rateB1 = $this->createStub(CurrencyAverageRate::class);
        $rateB2 = $this->createStub(CurrencyAverageRate::class);

        $tableA = $this->createMock(CurrencyAveragesTable::class);
        $tableA
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rateA1, $rateA2])
        ;

        $tableB = $this->createMock(CurrencyAveragesTable::class);
        $tableB
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rateB1, $rateB2])
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getCurrencyAveragesTableA')
            ->with(2023, 4)
            ->willReturn([$tableA])
        ;
        $repository
            ->expects(self::once())
            ->method('getCurrencyAveragesTableB')
            ->with(2023, 4)
            ->willReturn([$tableB])
        ;

        $service = new CurrencyAverageRatesService($repository);
        self::assertSame(
            [$rateA1, $rateA2, $rateB1, $rateB2],
            $service->fromMonth(2023, 4)->toArray()
        );
    }

    public function testFromDay(): void
    {
        $rateA2 = $this->createMock(CurrencyAverageRate::class);
        $rateB2 = $this->createMock(CurrencyAverageRate::class);

        $tableA1 = $this->createMock(CurrencyAveragesTable::class);
        $tableA1
            ->expects(self::exactly(2))
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-01'))
        ;

        $tableA2 = $this->createMock(CurrencyAveragesTable::class);
        $tableA2
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rateA2])
        ;
        $tableA2
            ->expects(self::exactly(2))
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-02'))
        ;

        $tableB1 = $this->createMock(CurrencyAveragesTable::class);
        $tableB1
            ->expects(self::exactly(2))
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-01'))
        ;

        $tableB2 = $this->createMock(CurrencyAveragesTable::class);
        $tableB2
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rateB2])
        ;
        $tableB2
            ->expects(self::exactly(2))
            ->method('getEffectiveDate')
            ->willReturn(new \DateTimeImmutable('2023-04-02'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getCurrencyAveragesTableA')
            ->with(2023, 4)
            ->willReturn([$tableA1, $tableA2])
        ;
        $repository
            ->expects(self::exactly(2))
            ->method('getCurrencyAveragesTableB')
            ->with(2023, 4)
            ->willReturn([$tableB1, $tableB2])
        ;

        $service = new CurrencyAverageRatesService($repository);
        self::assertSame(
            [$rateA2],
            $service->fromDay('2023-04-02')->fromTable('A')->getRates()
        );
        self::assertSame(
            [$rateB2],
            $service->fromDay('2023-04-02')->fromTable('B')->getRates()
        );
    }

    public function testFromDayNotFound(): void
    {
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(1))
            ->method('getCurrencyAveragesTableA')
            ->with(2023, 4)
            ->willReturn([])
        ;
        $repository
            ->expects(self::exactly(1))
            ->method('getCurrencyAveragesTableB')
            ->with(2023, 4)
            ->willReturn([])
        ;

        $service = new CurrencyAverageRatesService($repository);

        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'A\' was not found');

        $service->fromDay('2023-04-01')->fromTable('A');
    }

    public function testFromDayBefore(): void
    {
        $rateA1 = $this->createStub(CurrencyAverageRate::class);
        $rateB1 = $this->createStub(CurrencyAverageRate::class);

        $tableA1 = $this->createStub(CurrencyAveragesTable::class);
        $tableA1->method('getRates')->willReturn([$rateA1]);
        $tableA1->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-01'));

        $tableA2 = $this->createStub(CurrencyAveragesTable::class);
        $tableA2->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-02'));

        $tableB1 = $this->createStub(CurrencyAveragesTable::class);
        $tableB1->method('getRates')->willReturn([$rateB1]);
        $tableB1->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-01'));

        $tableB2 = $this->createStub(CurrencyAveragesTable::class);
        $tableB2->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-02'));

        $repository = $this->createMock(NbpRepository::class);

        $repository
            ->method('getCurrencyAveragesTableA')
            ->with(2023, 1)
            ->willReturn([$tableA1, $tableA2])
        ;

        $repository
            ->method('getCurrencyAveragesTableB')
            ->with(2023, 1)
            ->willReturn([$tableB1, $tableB2])
        ;

        $service = new CurrencyAverageRatesService($repository);
        self::assertSame(
            [$rateA1],
            $service->fromDayBefore('2023-01-02')->fromTable('A')->getRates()
        );
        self::assertSame(
            [$rateB1],
            $service->fromDayBefore('2023-01-02')->fromTable('B')->getRates()
        );
    }

    public function testFromDayBeforeJanuary1st(): void
    {
        $rateA1 = $this->createStub(CurrencyAverageRate::class);
        $rateB1 = $this->createStub(CurrencyAverageRate::class);

        $tableA1 = $this->createStub(CurrencyAveragesTable::class);
        $tableA1->method('getRates')->willReturn([$rateA1]);

        $tableA2 = $this->createStub(CurrencyAveragesTable::class);
        $tableA2->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-01'));

        $tableB1 = $this->createStub(CurrencyAveragesTable::class);
        $tableB1->method('getRates')->willReturn([$rateB1]);

        $tableB2 = $this->createStub(CurrencyAveragesTable::class);
        $tableB2->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-01-01'));

        $repository = $this->createStub(NbpRepository::class);

        $repository
            ->method('getCurrencyAveragesTableA')
            ->willReturnMap([
                [2022, 12, [$tableA1]],
                [2023, 1, [$tableA2]],
            ])
        ;

        $repository
            ->method('getCurrencyAveragesTableB')
            ->willReturnMap([
                [2022, 12, [$tableB1]],
                [2023, 1, [$tableB2]],
            ])
        ;

        $service = new CurrencyAverageRatesService($repository);
        self::assertSame(
            [$rateA1],
            $service->fromDayBefore('2023-01-01')->fromTable('A')->getRates()
        );
        self::assertSame(
            [$rateB1],
            $service->fromDayBefore('2023-01-01')->fromTable('B')->getRates()
        );
    }

    public function testFromDayBeforeInvalidTableA(): void
    {
        $repository = $this->createStub(NbpRepository::class);

        $repository
            ->method('getCurrencyAveragesTableA')
            ->willReturn([])
        ;

        $repository
            ->method('getCurrencyAveragesTableB')
            ->willReturn([])
        ;

        $service = new CurrencyAverageRatesService($repository);

        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'A\' was not found');

        $service->fromDayBefore('2023-04-04')->fromTable('A');
    }

    public function testFromDayBeforeInvalidTableB(): void
    {
        $repository = $this->createStub(NbpRepository::class);

        $table1 = $this->createStub(CurrencyAveragesTable::class);
        $table1->method('getEffectiveDate')->willReturn(new \DateTimeImmutable('2023-04-03'));

        $repository
            ->method('getCurrencyAveragesTableA')
            ->willReturn([$table1])
        ;

        $repository
            ->method('getCurrencyAveragesTableB')
            ->willReturn([])
        ;

        $service = new CurrencyAverageRatesService($repository);

        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'B\' was not found');

        $service->fromDayBefore('2023-04-04')->fromTable('B');
    }
}
