<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;

/**
 * @implements \IteratorAggregate<int, CurrencyTradingRate>
 * @internal
 */
final class RatesFlatCollection implements \IteratorAggregate
{
    /** @var iterable<CurrencyTradingTable> */
    private $tables;
    /** @var array<callable(CurrencyTradingTable, CurrencyTradingRate): bool> */
    private $conditions = [];

    /**
     * @param iterable<CurrencyTradingTable> $tables
     */
    public function __construct(iterable $tables)
    {
        $this->tables = $tables;
    }

    /**
     * @param callable(CurrencyTradingTable, CurrencyTradingRate): bool $predicate
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
            function (CurrencyTradingTable $table, CurrencyTradingRate $rate) use ($code) {
                return $rate->getCurrencyCode() === $code;
            }
        );
    }

    /**
     * @return \Iterator<CurrencyTradingRate>
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->tables as $table) {
            foreach ($table->getRates() as $rate) {
                if ($this->fulfillsConditions($table, $rate)) {
                    yield $rate;
                }
            }
        }
    }

    /**
     * @return array<CurrencyTradingRate>
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    private function fulfillsConditions(
        CurrencyTradingTable $table,
        CurrencyTradingRate $rate
    ): bool {
        foreach ($this->conditions as $predicate) {
            if (!$predicate($table, $rate)) {
                return false;
            }
        }

        return true;
    }
}
