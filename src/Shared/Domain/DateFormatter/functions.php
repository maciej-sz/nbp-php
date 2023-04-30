<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\DateFormatter;

use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;

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
        first_day_of_month($year, $month),
        last_day_of_month($year, $month),
    ];
}

function format_ym(int $year, int $month): string
{
    $timestamp = strtotime("{$year}-{$month}");
    if (false === $timestamp) {
        throw new InvalidDateException("Invalid year: {$year} and/or month: {$month}");
    }
    return date('Y-m', $timestamp);
}
