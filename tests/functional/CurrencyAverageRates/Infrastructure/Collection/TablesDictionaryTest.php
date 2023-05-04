<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Functional\CurrencyAverageRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection\TablesDictionary;
use MaciejSz\Nbp\Shared\Domain\Exception\TableNotFoundException;
use PHPUnit\Framework\TestCase;

class TablesDictionaryTest extends TestCase
{
    public function testFromTable(): void
    {
        $tableA = $this->createMock(CurrencyAveragesTable::class);
        $tableB = $this->createMock(CurrencyAveragesTable::class);
        $dict = new TablesDictionary([
            'A' => $tableA,
            'B' => $tableB,
        ]);

        self::assertSame($tableB, $dict->fromTable('B'));
    }

    public function testFromTableNotFound(): void
    {
        self::expectException(TableNotFoundException::class);
        self::expectExceptionMessage('Table with letter \'F\' was not found');

        (new TablesDictionary([]))->fromTable('F');
    }
}
