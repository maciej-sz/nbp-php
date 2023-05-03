<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Domain;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use PHPUnit\Framework\TestCase;

class CurrencyAverageRateTest extends TestCase
{
    public function testSetState(): void
    {
        $now = new \DateTimeImmutable();
        $instance = CurrencyAverageRate::__set_state([
            'currencyName' => 'foo dolar',
            'currencyCode' => 'FOO',
            'value' => 123.45,
            'effectiveDate' => $now,
        ]);

        self::assertSame('foo dolar', $instance->getCurrencyName());
        self::assertSame('FOO', $instance->getCurrencyCode());
        self::assertSame(123.45, $instance->getValue());
        self::assertSame($now, $instance->getEffectiveDate());
    }
}
