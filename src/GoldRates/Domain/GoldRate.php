<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Domain;

class GoldRate
{
    /** @var \DateTimeInterface */
    private $date;
    /** @var float */
    private $rate;

    public function __construct(\DateTimeInterface $date, float $rate)
    {
        $this->date = $date;
        $this->rate = $rate;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
