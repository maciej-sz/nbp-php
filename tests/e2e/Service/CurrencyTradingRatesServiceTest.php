<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\E2e\Service;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\Service\CurrencyTradingRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRatesServiceTest extends TestCase
{
    public function testGetMonthTables(): void
    {
        $currencyTrading = CurrencyTradingRatesService::create();
        $tablesIterable = $currencyTrading->getMonthTables(2010, 2);
        $tables = is_array($tablesIterable)
            ? $tablesIterable
            : iterator_to_array($tablesIterable);

        self::assertCount(20, $tables);
        self::assertContainsOnly(CurrencyTradingTable::class, $tables);
    }

    public function testFromMonth(): void
    {
        $currencyTrading = CurrencyTradingRatesService::create();
        $rates = $currencyTrading->fromMonth(2010, 2)->toArray();

        self::assertCount(280, $rates);
        self::assertContainsOnly(CurrencyTradingRate::class, $rates);
    }

    public function testFromEffectiveDay(): void
    {
        $currencyTrading = CurrencyTradingRatesService::create();
        $rate = $currencyTrading->fromEffectiveDay('2010-03-01')->getRate('USD');

        self::assertSame('2010-02-26', $rate->getTradingDate()->format('Y-m-d'));
        self::assertSame('2010-03-01', $rate->getEffectiveDate()->format('Y-m-d'));
        self::assertSame(2.8861, $rate->getBid());
        self::assertSame(2.9445, $rate->getAsk());
    }

    public function testFromEffectiveDayFailure(): void
    {
        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Rates table for effective date 2010-03-06 is not available');

        $currencyTrading = CurrencyTradingRatesService::create();
        $currencyTrading->fromEffectiveDay('2010-03-06');
    }

    public function testFromTradingDay(): void
    {
        $currencyTrading = CurrencyTradingRatesService::create();
        $rate = $currencyTrading->fromTradingDay('2010-04-01')->getRate('USD');

        self::assertSame('2010-04-01', $rate->getTradingDate()->format('Y-m-d'));
        self::assertSame('2010-04-02', $rate->getEffectiveDate()->format('Y-m-d'));
        self::assertSame(2.8140, $rate->getBid());
        self::assertSame(2.8708, $rate->getAsk());
    }

    public function testFromTradingDayFromNextMonth(): void
    {
        $currencyTrading = CurrencyTradingRatesService::create();
        $rate = $currencyTrading->fromTradingDay('2010-04-30')->getRate('USD');

        self::assertSame('2010-04-30', $rate->getTradingDate()->format('Y-m-d'));
        self::assertSame('2010-05-04', $rate->getEffectiveDate()->format('Y-m-d'));
        self::assertSame(2.9172, $rate->getBid());
        self::assertSame(2.9762, $rate->getAsk());
    }

    public function testFromTradingDayFailure(): void
    {
        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Rates table for trading date 2010-05-15 is not available');

        $currencyTrading = CurrencyTradingRatesService::create();
        $currencyTrading->fromTradingDay('2010-05-15');
    }
}
