<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Domain;

class CurrencyAverageRate
{
    /** @var string */
    private $currencyName;
    /** @var string */
    private $currencyCode;
    /** @var float */
    private $rate;
    /** @var \DateTimeInterface */
    private $effectiveDate;

    public function __construct(
        string $currencyName,
        string $currencyCode,
        float $rate,
        \DateTimeInterface $effectiveDate
    ) {
        $this->currencyName = $currencyName;
        $this->currencyCode = $currencyCode;
        $this->rate = $rate;
        $this->effectiveDate = $effectiveDate;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getEffectiveDate(): \DateTimeInterface
    {
        return $this->effectiveDate;
    }
}
