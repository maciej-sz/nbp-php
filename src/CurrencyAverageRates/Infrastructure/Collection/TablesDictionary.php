<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\Shared\Domain\Exception\TableNotFoundException;

/**
 * @internal
 */
final class TablesDictionary
{
    /** @var array<string, ?CurrencyAveragesTable> */
    private $tables;

    /**
     * @param array<string, ?CurrencyAveragesTable> $tables
     */
    public function __construct(array $tables)
    {
        $this->tables = $tables;
    }

    public function fromTable(string $letter): CurrencyAveragesTable
    {
        if (!isset($this->tables[$letter])) {
            throw new TableNotFoundException(
                "Table with letter '{$letter}' was not found"
            );
        }

        return $this->tables[$letter];
    }
}
