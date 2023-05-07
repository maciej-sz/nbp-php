<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Domain;

class GoldRate
{
    /** @var \DateTimeInterface */
    private $date;
    /** @var float */
    private $value;

    public function __construct(\DateTimeInterface $date, float $value)
    {
        $this->date = $date;
        $this->value = $value;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
