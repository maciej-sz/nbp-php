<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Domain;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\Shared\Domain\Exception\CurrencyCodeNotFoundException;
use PHPUnit\Framework\TestCase;

class CurrencyTradingTableTest extends TestCase
{
    public function testGetRate(): void
    {
        $rates = $this->getRatesFixture();
        $table = $this->getTableFixture($rates);

        self::assertSame($rates[0], $table->getRate('FOO'));
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
        $date1 = new \DateTimeImmutable();
        $date2 = new \DateTimeImmutable();
        $mockRate = $this->createMock(CurrencyTradingRate::class);
        $instance = CurrencyTradingTable::__set_state([
            'letter' => 'Z',
            'no' => '1/Z/ZZZ',
            'tradingDate' => $date1,
            'effectiveDate' => $date2,
            'rates' => ['BAR' => $mockRate],
        ]);

        self::assertSame('Z', $instance->getLetter());
        self::assertSame('1/Z/ZZZ', $instance->getNo());
        self::assertSame($date1, $instance->getTradingDate());
        self::assertSame($date2, $instance->getEffectiveDate());

        self::assertSame(['BAR' => $mockRate], $instance->getRates());
    }

    /**
     * @param array<CurrencyTradingRate> $rates
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
     * @return array<CurrencyTradingRate>
     */
    private function getRatesFixture(): array
    {
        return [
            new CurrencyTradingRate(
                'dolar testowy',
                'FOO',
                12.3,
                13.4,
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
        ];
    }
}
