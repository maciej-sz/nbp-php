<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\DateFormatter;

function first_day_of_month(int $year, int $month): string
{
    return date('Y-m-d', strtotime("{$year}-{$month}-01"));
}

function last_day_of_month(int $year, int $month): string
{
    return date('Y-m-t', strtotime("{$year}-{$month}"));
}

/**
 * @return array{startDate: string, endDate: string}
 */
function month_range(int $year, int $month): array
{
    return [
        'startDate' => first_day_of_month($year, $month),
        'endDate' => last_day_of_month($year, $month),
    ];
}

function format_ym(int $year, int $month): string
{
    return date('Y-m', strtotime("{$year}-{$month}"));
}
