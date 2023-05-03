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
            ->expects(self::once())
            ->method('getRates')
            ->willReturn([$rate1, $rate2])
        ;
        $collection = new RatesFlatCollection([$table]);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame([$rate1, $rate2], $items);
    }
}
