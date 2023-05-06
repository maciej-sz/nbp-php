<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Domain;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use PHPUnit\Framework\TestCase;

class CurrencyAveragesTableRegressionTest extends TestCase
{
    public function testMultipleCurrenciesWithSameCode(): void
    {
        $firstFooRate = $this->createStub(CurrencyAverageRate::class);
        $firstFooRate->method('getCurrencyCode')->willReturn('FOO');

        $secondFooRate = $this->createMock(CurrencyAverageRate::class);
        $secondFooRate->method('getCurrencyCode')->willReturn('FOO');

        $table = new CurrencyAveragesTable(
            'L',
            '1/L/2023',
            new \DateTimeImmutable(),
            [$firstFooRate, $secondFooRate]
        );

        self::assertSame($firstFooRate, $table->getRate('FOO'));
    }
}
