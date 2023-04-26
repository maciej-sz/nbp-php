<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Domain;

class CurrencyAveragesTable
{
    /** @var string */
    private $tableLetter;
    /** @var string */
    private $tableNumber;
    /** @var \DateTimeInterface */
    private $effectiveDate;
    /** @var array<CurrencyAverageRate> */
    private $rates;

    /**
     * @param array<CurrencyAverageRate> $rates
     */
    public function __construct(
        string $tableLetter,
        string $tableNumber,
        \DateTimeInterface $effectiveDate,
        array $rates
    ) {
        $this->tableLetter = $tableLetter;
        $this->tableNumber = $tableNumber;
        $this->effectiveDate = $effectiveDate;
        $this->rates = $rates;
    }

    public function getTableLetter(): string
    {
        return $this->tableLetter;
    }

    public function getTableNumber(): string
    {
        return $this->tableNumber;
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
