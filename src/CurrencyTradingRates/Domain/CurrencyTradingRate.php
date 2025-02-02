<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Domain;

class CurrencyTradingRate
{
    /** @var string */
    private $currencyName;
    /** @var string */
    private $currencyCode;
    /** @var float */
    private $bid;
    /** @var float */
    private $ask;
    /** @var \DateTimeInterface */
    private $tradingDate;
    /** @var \DateTimeInterface */
    private $effectiveDate;

    public function __construct(
        string $currencyName,
        string $currencyCode,
        float $bid,
        float $ask,
        \DateTimeInterface $tradingDate,
        \DateTimeInterface $effectiveDate,
    ) {
        $this->currencyName = $currencyName;
        $this->currencyCode = $currencyCode;
        $this->bid = $bid;
        $this->ask = $ask;
        $this->tradingDate = $tradingDate;
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

    public function getBid(): float
    {
        return $this->bid;
    }

    public function getAsk(): float
    {
        return $this->ask;
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
     * @param array{
     *     currencyName: string,
     *     currencyCode: string,
     *     bid: float,
     *     ask: float,
     *     tradingDate: \DateTimeInterface,
     *     effectiveDate: \DateTimeInterface
     * } $data
     */
    public static function __set_state(array $data): self
    {
        return new self(
            $data['currencyName'],
            $data['currencyCode'],
            $data['bid'],
            $data['ask'],
            $data['tradingDate'],
            $data['effectiveDate']
        );
    }
}
