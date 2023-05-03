<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Domain;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRateTest extends TestCase
{
    public function testSetState(): void
    {
        $date1 = new \DateTimeImmutable();
        $date2 = new \DateTimeImmutable();
        $instance = CurrencyTradingRate::__set_state([
            'currencyName' => 'bar dolar',
            'currencyCode' => 'BAR',
            'bid' => 123.45,
            'ask' => 543.21,
            'tradingDate' => $date1,
            'effectiveDate' => $date2,
        ]);

        self::assertSame('bar dolar', $instance->getCurrencyName());
        self::assertSame('BAR', $instance->getCurrencyCode());
        self::assertSame(123.45, $instance->getBid());
        self::assertSame(543.21, $instance->getAsk());
        self::assertSame($date1, $instance->getTradingDate());
        self::assertSame($date2, $instance->getEffectiveDate());
    }
}
