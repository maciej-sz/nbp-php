<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Domain;

use MaciejSz\Nbp\Shared\Domain\Exception\CurrencyCodeNotFoundException;

class CurrencyTradingTable
{
    /** @var string */
    private $letter;
    /** @var string */
    private $no;
    /** @var \DateTimeInterface */
    private $tradingDate;
    /** @var \DateTimeInterface */
    private $effectiveDate;
    /** @var array<CurrencyTradingRate> */
    private $rates;

    /**
     * @param array<CurrencyTradingRate> $rates
     */
    public function __construct(
        string $tableLetter,
        string $tableNo,
        \DateTimeInterface $tradingDate,
        \DateTimeInterface $effectiveDate,
        array $rates
    ) {
        $this->letter = $tableLetter;
        $this->no = $tableNo;
        $this->tradingDate = $tradingDate;
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

    public function getTradingDate(): \DateTimeInterface
    {
        return $this->tradingDate;
    }

    public function getEffectiveDate(): \DateTimeInterface
    {
        return $this->effectiveDate;
    }

    /**
     * @return array<string, CurrencyTradingRate>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRate(string $code): CurrencyTradingRate
    {
        foreach ($this->rates as $rate) {
            if ($rate->getCurrencyCode() === $code) {
                return $rate;
            }
        }

        throw new CurrencyCodeNotFoundException(
            "Currency code: '{$code}' not found in table '{$this->no}'"
        );
    }

    /**
     * @param array{
     *     letter: string,
     *     no: string,
     *     tradingDate: \DateTimeInterface,
     *     effectiveDate: \DateTimeInterface,
     *     rates: array<string, CurrencyTradingRate>,
     * } $data
     */
    public static function __set_state(array $data): self
    {
        return new self(
            $data['letter'],
            $data['no'],
            $data['tradingDate'],
            $data['effectiveDate'],
            $data['rates']
        );
    }
}
