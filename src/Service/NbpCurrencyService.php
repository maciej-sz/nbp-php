<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Service;

use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;

class NbpCurrencyService
{
    public function __construct(NbpRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getMonthRates(int $year, int $month)
    {
        $startDate = $this->dateHelper->firstDayOfMonth($year, $month);
        $endDate = $this->dateHelper->lastDayOfMonth($year, $month);
    }

    public function getAverageRate()
    {
    }
}
