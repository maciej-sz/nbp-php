<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;

/**
 * @implements \IteratorAggregate<int, CurrencyAverageRate>
 * @internal
 */
final class RatesFlatCollection implements \IteratorAggregate
{
    /** @var iterable<CurrencyAveragesTable> */
    private $tablesA;
    /** @var iterable<CurrencyAveragesTable> */
    private $tablesB;
    /** @var array<callable(CurrencyAveragesTable, CurrencyAverageRate): bool> */
    private $conditions = [];

    /**
     * @param iterable<CurrencyAveragesTable> $tablesA
     * @param iterable<CurrencyAveragesTable> $tablesB
     */
    public function __construct(iterable $tablesA, iterable $tablesB)
    {
        $this->tablesA = $tablesA;
        $this->tablesB = $tablesB;
    }

    /**
     * @param callable(CurrencyAveragesTable, CurrencyAverageRate): bool $predicate
     */
    public function where(callable $predicate): self
    {
        $instance = clone $this;
        $instance->conditions[] = $predicate;

        return $instance;
    }

    public function whereCurrency(string $code): self
    {
        return $this->where(
            function (CurrencyAveragesTable $table, CurrencyAverageRate $rate) use ($code) {
                return $rate->getCurrencyCode() === $code;
            }
        );
    }

    public function fromTable(string $letter): self
    {
        return $this->where(
            function (CurrencyAveragesTable $table, CurrencyAverageRate $rate) use ($letter) {
                return $table->getLetter() === $letter;
            }
        );
    }

    /**
     * @return \Iterator<CurrencyAverageRate>
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->tablesA as $table) {
            foreach ($table->getRates() as $rate) {
                if ($this->fulfillsConditions($table, $rate)) {
                    yield $rate;
                }
            }
        }
        foreach ($this->tablesB as $table) {
            foreach ($table->getRates() as $rate) {
                if ($this->fulfillsConditions($table, $rate)) {
                    yield $rate;
                }
            }
        }
    }

    /**
     * @return array<CurrencyAverageRate>
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    private function fulfillsConditions(
        CurrencyAveragesTable $table,
        CurrencyAverageRate $rate,
    ): bool {
        foreach ($this->conditions as $predicate) {
            if (!$predicate($table, $rate)) {
                return false;
            }
        }

        return true;
    }
}
