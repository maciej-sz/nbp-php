<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Domain;

use MaciejSz\Nbp\Shared\Domain\Exception\CurrencyCodeNotFoundException;

class CurrencyAveragesTable
{
    public const A = 'A';
    public const B = 'B';

    /** @var string */
    private $letter;
    /** @var string */
    private $no;
    /** @var \DateTimeInterface */
    private $effectiveDate;
    /** @var array<string, CurrencyAverageRate> */
    private $rates;

    /**
     * @param array<string, CurrencyAverageRate> $rates
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

    public function getRate(string $code): CurrencyAverageRate
    {
        if (!isset($this->rates[$code])) {
            throw new CurrencyCodeNotFoundException(
                "Currency code: '{$code}' not found in table '{$this->no}'"
            );
        }

        return $this->rates[$code];
    }
}
