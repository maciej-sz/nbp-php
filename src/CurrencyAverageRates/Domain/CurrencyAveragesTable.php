<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Domain;

class CurrencyAveragesTable
{
    /** @var string */
    private $letter;
    /** @var string */
    private $no;
    /** @var \DateTimeInterface */
    private $effectiveDate;
    /** @var array<CurrencyAverageRate> */
    private $rates;

    /**
     * @param array<CurrencyAverageRate> $rates
     */
    public function __construct(
        string $letter,
        string $no,
        \DateTimeInterface $effectiveDate,
        array $rates
    ) {
        $this->letter = $letter;
        $this->no = $no;
        $this->effectiveDate = $effectiveDate;
        $this->rates = $rates;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getNo(): string
    {
        return $this->no;
    }

    public function getEffectiveDate(): \DateTimeInterface
    {
        return $this->effectiveDate;
    }

    public function getRates(): array
    {
        return $this->rates;
    }
}
