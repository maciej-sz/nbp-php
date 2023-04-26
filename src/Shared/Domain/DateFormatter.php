<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain;

class DateFormatter
{
    public function firstDayOfMonth(int $year, int $month): string
    {
        return date('Y-m-d', strtotime("{$year}-{$month}-01"));
    }

    public function lastDayOfMonth(int $year, int $month): string
    {
        return date('Y-m-t', strtotime("{$year}-{$month}"));
    }

    public function yearAndMonthFormat(int $year, int $month): string
    {
        return date('Y-m', strtotime("{$year}-{$month}"));
    }
}
