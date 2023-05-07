<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Collection\RatesFlatCollection;
use PHPUnit\Framework\TestCase;

class RatesFlatCollectionTest extends TestCase
{
    public function testGetIterator(): void
    {
        $rate1 = $this->createMock(CurrencyTradingRate::class);
        $rate2 = $this->createMock(CurrencyTradingRate::class);
        $table = $this->createMock(CurrencyTradingTable::class);
        $table
            ->expects(self::exactly(2))
            ->method('getRates')
            ->willReturn([$rate1, $rate2])
        ;
        $collection = new RatesFlatCollection([$table]);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame([$rate1, $rate2], $items);
        self::assertSame([$rate1, $rate2], $collection->toArray());
    }

    public function testWhere(): void
    {
        $rate1 = $this->createMock(CurrencyTradingRate::class);
        $rate2 = $this->createMock(CurrencyTradingRate::class);
        $table = $this->createMock(CurrencyTradingTable::class);
        $table->method('getRates')->willReturn([$rate1, $rate2]);
        $collection = new RatesFlatCollection([$table]);

        $collection = $collection->where(
            function (CurrencyTradingTable $table, CurrencyTradingRate $rate) use ($rate2) {
                return $rate === $rate2;
            }
        );

        self::assertSame([$rate2], $collection->toArray());
    }

    public function testWhereCurrency(): void
    {
        $rate1 = $this->createMock(CurrencyTradingRate::class);
        $rate2 = $this->createMock(CurrencyTradingRate::class);

        $rate1->expects(self::once())->method('getCurrencyCode')->willReturn('USD');
        $rate2->expects(self::once())->method('getCurrencyCode')->willReturn('EUR');

        $table = $this->createMock(CurrencyTradingTable::class);
        $table->method('getRates')->willReturn([$rate1, $rate2]);
        $collection = new RatesFlatCollection([$table]);

        $collection = $collection->whereCurrency('EUR');

        self::assertSame([$rate2], $collection->toArray());
    }
}
