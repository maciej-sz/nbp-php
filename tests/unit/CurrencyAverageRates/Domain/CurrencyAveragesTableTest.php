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

    public function testSetState(): void
    {
        $now = new \DateTimeImmutable();
        $mockRate = $this->createMock(CurrencyAverageRate::class);

        $instance = CurrencyAveragesTable::__set_state([
            'letter' => 'F',
            'no' => '1/F/NOW',
            'effectiveDate' => $now,
            'rates' => ['FOO' => $mockRate],
        ]);

        self::assertSame('F', $instance->getLetter());
        self::assertSame('1/F/NOW', $instance->getNo());
        self::assertSame($now, $instance->getEffectiveDate());
        self::assertSame(['FOO' => $mockRate], $instance->getRates());
    }

    /**
     * @param array<string, CurrencyAverageRate> $rates
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
            ),
        ];
    }
}
