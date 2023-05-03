<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Collection;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;

/**
 * @internal
 */
final class RatesFlatCollection
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
            function(CurrencyTradingTable $table, CurrencyTradingRate $rate) use ($code) {
                return $rate->getCurrencyCode() === $code;
            }
        );
    }

    /**
     * @return iterable<CurrencyTradingRate>
     */
    public function getRates(): iterable
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
        /** @var \Generator $rates */
        $rates = $this->getRates();

        return iterator_to_array($rates);
    }

    private function fulfillsConditions(
        CurrencyTradingTable $table,
        CurrencyTradingRate $rate
    ): bool
    {
        foreach ($this->conditions as $predicate) {
            if (!$predicate($table, $rate)) {
                return false;
            }
        }

        return true;
    }
}
