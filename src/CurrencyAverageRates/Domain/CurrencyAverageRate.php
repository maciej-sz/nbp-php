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
    private $value;
    /** @var \DateTimeInterface */
    private $effectiveDate;

    public function __construct(
        string $currencyName,
        string $currencyCode,
        float $value,
        \DateTimeInterface $effectiveDate
    ) {
        $this->currencyName = $currencyName;
        $this->currencyCode = $currencyCode;
        $this->value = $value;
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function getEffectiveDate(): \DateTimeInterface
    {
        return $this->effectiveDate;
    }

    /**
     * @param array{
     *     currencyName: string,
     *     currencyCode: string,
     *     value: float,
     *     effectiveDate: \DateTimeInterface
     * } $data
     */
    public static function __set_state(array $data): self
    {
        return new self(
            $data['currencyName'],
            $data['currencyCode'],
            $data['value'],
            $data['effectiveDate']
        );
    }
}
