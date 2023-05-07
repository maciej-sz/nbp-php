<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\GoldRates\Infrastructure\Collection;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\GoldRates\Infrastructure\Collection\GoldRatesCollection;
use PHPUnit\Framework\TestCase;

class GoldRatesCollectionTest extends TestCase
{
    public function testIterator(): void
    {
        $rate1 = $this->createMock(GoldRate::class);
        $rate2 = $this->createMock(GoldRate::class);
        $rates = [$rate1, $rate2];
        $collection = new GoldRatesCollection($rates);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertSame($rates, $items);
        self::assertSame($rates, $collection->toArray());
    }
}
