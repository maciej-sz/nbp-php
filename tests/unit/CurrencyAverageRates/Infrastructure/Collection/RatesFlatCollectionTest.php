<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection\RatesFlatCollection;
use PHPUnit\Framework\TestCase;

class RatesFlatCollectionTest extends TestCase
{
    public function testIterator(): void
    {
        $rateA1 = $this->createMock(CurrencyAverageRate::class);
        $rateA2 = $this->createMock(CurrencyAverageRate::class);
        $rateB1 = $this->createMock(CurrencyAverageRate::class);
        $rateB2 = $this->createMock(CurrencyAverageRate::class);

        $tableA1 = $this->createMock(CurrencyAveragesTable::class);
        $tableA2 = $this->createMock(CurrencyAveragesTable::class);
        $tableB1 = $this->createMock(CurrencyAveragesTable::class);
        $tableB2 = $this->createMock(CurrencyAveragesTable::class);

        $tableA1->expects(self::exactly(2))->method('getRates')->willReturn([$rateA1]);
        $tableA2->expects(self::exactly(2))->method('getRates')->willReturn([$rateA2]);
        $tableB1->expects(self::exactly(2))->method('getRates')->willReturn([$rateB1]);
        $tableB2->expects(self::exactly(2))->method('getRates')->willReturn([$rateB2]);

        $collection = new RatesFlatCollection([$tableA1, $tableA2], [$tableB1, $tableB2]);
        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame([$rateA1, $rateA2, $rateB1, $rateB2], $items);
        self::assertSame([$rateA1, $rateA2, $rateB1, $rateB2], $collection->toArray());
    }

    public function testWhere(): void
    {
        $rateA1 = $this->createMock(CurrencyAverageRate::class);
        $rateA2 = $this->createMock(CurrencyAverageRate::class);
        $rateB1 = $this->createMock(CurrencyAverageRate::class);
        $rateB2 = $this->createMock(CurrencyAverageRate::class);

        $tableA1 = $this->createMock(CurrencyAveragesTable::class);
        $tableA2 = $this->createMock(CurrencyAveragesTable::class);
        $tableB1 = $this->createMock(CurrencyAveragesTable::class);
        $tableB2 = $this->createMock(CurrencyAveragesTable::class);

        $tableA1->expects(self::once())->method('getRates')->willReturn([$rateA1]);
        $tableA2->expects(self::once())->method('getRates')->willReturn([$rateA2]);
        $tableB1->expects(self::once())->method('getRates')->willReturn([$rateB1]);
        $tableB2->expects(self::once())->method('getRates')->willReturn([$rateB2]);

        $collection = new RatesFlatCollection([$tableA1, $tableA2], [$tableB1, $tableB2]);
        $collection = $collection->where(
            function(CurrencyAveragesTable $table, CurrencyAverageRate $rate) use ($tableB2) {
                return $table === $tableB2;
            }
        );

        self::assertSame([$rateB2], $collection->toArray());
    }

    public function testWhereCurrency(): void
    {
        $rateA1 = $this->createMock(CurrencyAverageRate::class);
        $rateA1->expects(self::once())->method('getCurrencyCode')->willReturn('USD');
        $rateA2 = $this->createMock(CurrencyAverageRate::class);
        $rateA2->expects(self::once())->method('getCurrencyCode')->willReturn('EUR');
        $rateB1 = $this->createMock(CurrencyAverageRate::class);
        $rateB1->expects(self::once())->method('getCurrencyCode')->willReturn('CHF');
        $rateB2 = $this->createMock(CurrencyAverageRate::class);
        $rateB2->expects(self::once())->method('getCurrencyCode')->willReturn('GBP');

        $tableA1 = $this->createMock(CurrencyAveragesTable::class);
        $tableA2 = $this->createMock(CurrencyAveragesTable::class);
        $tableB1 = $this->createMock(CurrencyAveragesTable::class);
        $tableB2 = $this->createMock(CurrencyAveragesTable::class);

        $tableA1->expects(self::once())->method('getRates')->willReturn([$rateA1]);
        $tableA2->expects(self::once())->method('getRates')->willReturn([$rateA2]);
        $tableB1->expects(self::once())->method('getRates')->willReturn([$rateB1]);
        $tableB2->expects(self::once())->method('getRates')->willReturn([$rateB2]);

        $collection = new RatesFlatCollection([$tableA1, $tableA2], [$tableB1, $tableB2]);
        $collection = $collection->whereCurrency('CHF');

        self::assertSame([$rateB1], $collection->toArray());
    }

    public function testFromTable(): void
    {
        $rateA1 = $this->createMock(CurrencyAverageRate::class);
        $rateA2 = $this->createMock(CurrencyAverageRate::class);
        $rateB1 = $this->createMock(CurrencyAverageRate::class);
        $rateB2 = $this->createMock(CurrencyAverageRate::class);

        $tableA1 = $this->createMock(CurrencyAveragesTable::class);
        $tableA1->expects(self::once())->method('getLetter')->willReturn('A');
        $tableA2 = $this->createMock(CurrencyAveragesTable::class);
        $tableA2->expects(self::once())->method('getLetter')->willReturn('A');
        $tableB1 = $this->createMock(CurrencyAveragesTable::class);
        $tableB1->expects(self::once())->method('getLetter')->willReturn('B');
        $tableB2 = $this->createMock(CurrencyAveragesTable::class);
        $tableB2->expects(self::once())->method('getLetter')->willReturn('B');

        $tableA1->expects(self::once())->method('getRates')->willReturn([$rateA1]);
        $tableA2->expects(self::once())->method('getRates')->willReturn([$rateA2]);
        $tableB1->expects(self::once())->method('getRates')->willReturn([$rateB1]);
        $tableB2->expects(self::once())->method('getRates')->willReturn([$rateB2]);

        $collection = new RatesFlatCollection([$tableA1, $tableA2], [$tableB1, $tableB2]);
        $collection = $collection->fromTable('A');

        self::assertSame([$rateA1, $rateA2], $collection->toArray());
    }
}
