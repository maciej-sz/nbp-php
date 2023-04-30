<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Domain;

class CurrencyTradingTable
{
    /** @var string */
    private $tableLetter;
    /** @var string */
    private $tableNumber;
    /** @var \DateTimeInterface */
    private $tradingDate;
    /** @var \DateTimeInterface */
    private $effectiveDate;
    /** @var array<string, CurrencyTradingRate> */
    private $rates;

    /**
     * @param array<string, CurrencyTradingRate> $rates
     */
    public function __construct(
        string $tableLetter,
        string $tableNumber,
        \DateTimeInterface $tradingDate,
        \DateTimeInterface $effectiveDate,
        array $rates
    ) {
        $this->tableLetter = $tableLetter;
        $this->tableNumber = $tableNumber;
        $this->tradingDate = $tradingDate;
        $this->effectiveDate = $effectiveDate;
        $this->rates = $rates;
    }

    public function getLetter(): string
    {
        return $this->tableLetter;
    }

    public function getNumber(): string
    {
        return $this->tableNumber;
    }

    public function getTradingDate(): \DateTimeInterface
    {
        return $this->tradingDate;
    }

    public function getEffectiveDate(): \DateTimeInterface
    {
        return $this->effectiveDate;
    }

    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRate(string $code): CurrencyTradingRate
    {
        if (!isset($this->rates[$code])) {
            throw new \OutOfRangeException("Currency code: '{$code}' not found in table '{$this->tableNumber}'");
        }

        return $this->rates[$code];
    }
}
