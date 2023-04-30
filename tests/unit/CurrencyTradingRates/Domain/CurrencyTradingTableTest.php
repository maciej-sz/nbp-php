<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Domain;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use PHPUnit\Framework\TestCase;

class CurrencyTradingTableTest extends TestCase
{
    public function testGetRate(): void
    {
        $rates = $this->getRatesFixture();
        $table = $this->getTableFixture($rates);

        self::assertSame($rates['FOO'], $table->getRate('FOO'));
    }

    public function testGetRateFailure(): void
    {
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage(
            'Currency code: \'BOGUS\' not found in table \'042/T/NBP/2023\''
        );

        $table = $this->getTableFixture([]);
        $table->getRate('BOGUS');
    }

    /**
     * @param array<string, CurrencyTradingRate> $rates
     */
    private function getTableFixture(array $rates): CurrencyTradingTable
    {
        return new CurrencyTradingTable(
            'T',
            '042/T/NBP/2023',
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            $rates
        );
    }

    /**
     * @return array<string, CurrencyTradingRate>
     */
    private function getRatesFixture(): array
    {
        return [
            'FOO' => new CurrencyTradingRate(
                'FOO',
                'dolar testowy',
                12.3,
                13.4,
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        ];
    }
}
