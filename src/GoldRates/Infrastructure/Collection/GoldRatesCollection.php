<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Infrastructure\Collection;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;

/**
 * @implements \IteratorAggregate<int, GoldRate>
 */
class GoldRatesCollection implements \IteratorAggregate
{
    /** @var iterable<GoldRate> */
    private $rates;

    /**
     * @param iterable<GoldRate> $rates
     */
    public function __construct(iterable $rates)
    {
        $this->rates = $rates;
    }

    /**
     * @return \Iterator<GoldRate>
     */
    public function getIterator(): \Iterator
    {
        yield from $this->rates;
    }

    /**
     * @return array<GoldRate>
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }
}
