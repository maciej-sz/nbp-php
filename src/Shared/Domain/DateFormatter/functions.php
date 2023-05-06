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
 * @return array{string, string}
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

/**
 * @param string|\DateTimeInterface $date
 * @return array{int, int}
 */
function extract_ym($date): array
{
    $dateObj = ensure_date_obj($date);

    return [
        (int) $dateObj->format('Y'),
        (int) $dateObj->format('n'),
    ];
}

/**
 * @param string|\DateTimeInterface $date1
 * @param string|\DateTimeInterface $date2
 */
function is_same_day($date1, $date2): bool
{
    $dateObj1 = ensure_date_obj($date1);
    $dateObj2 = ensure_date_obj($date2);

    return $dateObj1->format('Y-m-d') === $dateObj2->format('Y-m-d');
}

/**
 * @param string|\DateTimeInterface $date1
 * @param string|\DateTimeInterface $date2
 */
function compare_days($date1, $date2): int
{
    $dateObj1 = ensure_date_obj($date1);
    $dateObj2 = ensure_date_obj($date2);

    return $dateObj1->format('Y-m-d') <=> $dateObj2->format('Y-m-d');
}

/**
 * @param string|\DateTimeInterface $date
 */
function previous_month($date): \DateTimeInterface
{
    return middle_of_month($date)->sub(new \DateInterval('P1M'));
}

/**
 * @param string|\DateTimeInterface $date
 */
function next_month($date): \DateTimeInterface
{
    return middle_of_month($date)->add(new \DateInterval('P1M'));
}

function middle_of_month($date): \DateTimeInterface
{
    return new \DateTimeImmutable(ensure_date_obj($date)->format('Y-m-') . '15');
}

/**
 * @param string|\DateTimeInterface $date
 */
function ensure_date_obj($date): \DateTimeImmutable
{
    return ($date instanceof \DateTimeInterface)
        ? new \DateTimeImmutable($date->format('c'))
        : new \DateTimeImmutable($date)
    ;
}
