<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\E2e\Service;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\TableNotFoundException;
use PHPUnit\Framework\TestCase;

class CurrencyAverageRatesServiceTest extends TestCase
{
    public function testGetMonthsTableA(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $tablesIterable = $currencyAverages->getMonthTablesA(2011, 1);
        $tables = is_array($tablesIterable)
            ? $tablesIterable
            : iterator_to_array($tablesIterable);

        self::assertCount(20, $tables);
        self::assertContainsOnly(CurrencyAveragesTable::class, $tables);
    }

    public function testGetMonthsTableB(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $tablesIterable = $currencyAverages->getMonthTablesB(2011, 1);
        $tables = is_array($tablesIterable)
            ? $tablesIterable
            : iterator_to_array($tablesIterable);

        self::assertCount(4, $tables);
        self::assertContainsOnly(CurrencyAveragesTable::class, $tables);
    }

    public function testFromMonth(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $rates = $currencyAverages->fromMonth(2011, 2)->toArray();

        self::assertCount(1220, $rates);
        self::assertContainsOnly(CurrencyAverageRate::class, $rates);
    }

    public function testFromDay(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $tables = $currencyAverages->fromDay('2011-03-02');

        $tableA = $tables->fromTable('A');
        $ratesTableA = $tableA->getRates();

        $tableB = $tables->fromTable('B');
        $ratesTableB = $tableB->getRates();

        self::assertSame('2011-03-02', $tableA->getEffectiveDate()->format('Y-m-d'));
        self::assertSame('2011-03-02', $tableB->getEffectiveDate()->format('Y-m-d'));
        self::assertContainsOnly(CurrencyAverageRate::class, $ratesTableA);
        self::assertContainsOnly(CurrencyAverageRate::class, $ratesTableB);
        self::assertCount(1, array_filter($ratesTableA, function(CurrencyAverageRate $rate){
            return $rate->getCurrencyCode() === 'USD';
        }));
        self::assertCount(0, array_filter($ratesTableA, function(CurrencyAverageRate $rate){
            return $rate->getCurrencyCode() === 'AED';
        }));
        self::assertCount(1, array_filter($ratesTableB, function(CurrencyAverageRate $rate){
            return $rate->getCurrencyCode() === 'AED';
        }));
        self::assertCount(0, array_filter($ratesTableB, function(CurrencyAverageRate $rate){
            return $rate->getCurrencyCode() === 'USD';
        }));
        self::assertCount(34, $ratesTableA);
        self::assertCount(135, $ratesTableB);
    }

    public function testFromDayWithTableANotAvailable(): void
    {
        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'B\' was not found');

        $currencyAverages = CurrencyAverageRatesService::create();
        $tables = $currencyAverages->fromDay('2011-03-03');

        $tableA = $tables->fromTable('A');
        self::assertInstanceOf(CurrencyAveragesTable::class, $tableA);

        $tables->fromTable('B');
    }

    public function testFromDayBefore(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $tables = $currencyAverages->fromDayBefore('2011-03-09');

        $tableA = $tables->fromTable('A');
        $tableB = $tables->fromTable('B');

        self::assertSame('2011-03-08', $tableA->getEffectiveDate()->format('Y-m-d'));
        self::assertSame('2011-03-02', $tableB->getEffectiveDate()->format('Y-m-d'));
    }

    public function testFromDayBeforeBreakOfYear(): void
    {
        $currencyAverages = CurrencyAverageRatesService::create();
        $tables = $currencyAverages->fromDayBefore('2012-01-02');

        $tableA = $tables->fromTable('A');
        $tableB = $tables->fromTable('B');

        self::assertSame('2011-12-30', $tableA->getEffectiveDate()->format('Y-m-d'));
        self::assertSame('2011-12-28', $tableB->getEffectiveDate()->format('Y-m-d'));
    }
}
