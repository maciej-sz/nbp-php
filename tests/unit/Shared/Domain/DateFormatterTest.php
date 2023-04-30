<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Domain;

use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;
use PHPUnit\Framework\TestCase;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\first_day_of_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\format_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\last_day_of_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\month_range;

class DateFormatterTest extends TestCase
{
    public function testFirstDayOfMonth(): void
    {
        $this->assertSame('2022-01-01', first_day_of_month(2022, 1));
        $this->assertSame('1234-12-01', first_day_of_month(1234, 12));
    }

    public function testLastDayOfMonth(): void
    {
        $this->assertSame('2023-03-31', last_day_of_month(2023, 3));
        $this->assertSame('2020-02-29', last_day_of_month(2020, 2));
    }

    public function testMonthRange(): void
    {
        $this->assertSame(
            ['2023-03-01', '2023-03-31'],
            month_range(2023, 3)
        );

        $this->assertSame(
            ['2020-02-01', '2020-02-29'],
            month_range(2020, 2)
        );
    }

    public function testFormatYm()
    {
        self::assertSame('2020-09', format_ym(2020, 9));
        self::assertSame('1970-01', format_ym(1970, 1));
    }

    public function testFormatYmInvalid()
    {
        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Invalid year: 2020 and/or month: 13');

        format_ym(2020, 13);
    }
}
