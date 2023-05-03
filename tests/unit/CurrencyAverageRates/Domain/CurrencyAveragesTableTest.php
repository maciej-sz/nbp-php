<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Domain;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\Shared\Domain\Exception\CurrencyCodeNotFoundException;
use PHPUnit\Framework\TestCase;

class CurrencyAveragesTableTest extends TestCase
{
    public function testGetRate(): void
    {
        $rates = $this->getRatesFixture();
        $table = $this->getTableFixture($rates);

        self::assertSame($rates['FOO'], $table->getRate('FOO'));
    }

    public function testGetRateFailure(): void
    {
        $this->expectException(CurrencyCodeNotFoundException::class);
        $this->expectExceptionMessage(
            'Currency code: \'BOGUS\' not found in table \'042/T/NBP/2023\''
        );

        $table = $this->getTableFixture([]);
        $table->getRate('BOGUS');
    }

    /**
     * @param array<string, CurrencyAveragesTable> $rates
     */
    private function getTableFixture(array $rates): CurrencyAveragesTable
    {
        return new CurrencyAveragesTable(
            'T',
            '042/T/NBP/2023',
            new \DateTimeImmutable(),
            $rates
        );
    }

    /**
     * @return array<string, CurrencyAverageRate>
     */
    private function getRatesFixture(): array
    {
        return [
            'FOO' => new CurrencyAverageRate(
                'FOO',
                'dolar testowy',
                12.3,
                new \DateTimeImmutable()
            )
        ];
    }
}
