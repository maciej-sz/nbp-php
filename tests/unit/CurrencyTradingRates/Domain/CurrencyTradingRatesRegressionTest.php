<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Domain;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRatesRegressionTest extends TestCase
{
    public function testMultipleCurrenciesWithSameCode(): void
    {
        $firstFooRate = $this->createStub(CurrencyTradingRate::class);
        $firstFooRate->method('getCurrencyCode')->willReturn('FOO');

        $secondFooRate = $this->createMock(CurrencyTradingRate::class);
        $secondFooRate->method('getCurrencyCode')->willReturn('FOO');

        $table = new CurrencyTradingTable(
            'L',
            '1/L/2023',
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            [$firstFooRate, $secondFooRate]
        );

        self::assertSame($firstFooRate, $table->getRate('FOO'));
    }
}
