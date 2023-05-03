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
    /** @var array<string, CurrencyTradingRate> */
    private $rates;

    /**
     * @param array<string, CurrencyTradingRate> $rates
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
        if (!isset($this->rates[$code])) {
            throw new CurrencyCodeNotFoundException(
                "Currency code: '{$code}' not found in table '{$this->no}'"
            );
        }

        return $this->rates[$code];
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
