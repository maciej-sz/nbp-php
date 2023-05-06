<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Domain;

use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;
use PHPUnit\Framework\TestCase;

use function MaciejSz\Nbp\Shared\Domain\DateFormatter\compare_days;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\extract_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\first_day_of_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\format_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\is_same_day;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\last_day_of_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\month_range;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\next_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\previous_month;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\safe_strtotime;

class DateFormatterTest extends TestCase
{
    public function testSafeStrToTime(): void
    {
        self::assertSame(1680652800, safe_strtotime('2023-04-05'));
    }

    public function testSafeStrToTimeInvalid(): void
    {
        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Cannot convert string to time');

        safe_strtotime('bogus');
    }

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

    public function testFormatYm(): void
    {
        self::assertSame('2020-09', format_ym(2020, 9));
        self::assertSame('1970-01', format_ym(1970, 1));
    }

    public function testFormatYmInvalid(): void
    {
        self::expectException(InvalidDateException::class);
        self::expectExceptionMessage('Invalid year: 2020 and/or month: 13');

        format_ym(2020, 13);
    }

    public function testExtractYm(): void
    {
        self::assertSame([2023, 1], extract_ym('2023-01'));
        self::assertSame([1234, 12], extract_ym('1234-12'));
    }

    /**
     * @dataProvider isSameDayDataProvider
     * @param string|\DateTimeInterface $date1
     * @param string|\DateTimeInterface $date2
     */
    public function testIsSameDay($date1, $date2, bool $expected): void
    {
        self::assertSame($expected, is_same_day($date1, $date2));
    }

    public function testCompareDays(): void
    {
        self::assertSame(-1, compare_days('2020-01-01', '2020-01-02'));
        self::assertSame(0, compare_days('2020-01-01', '2020-01-01'));
        self::assertSame(1, compare_days('2020-01-02', '2020-01-01'));
    }

    public function testPreviousMonth(): void
    {
        self::assertSame('2023-02', previous_month('2023-03')->format('Y-m'));
        self::assertSame('2022-12', previous_month('2023-01-01T00:00:00')->format('Y-m'));
        self::assertSame('2023-02', previous_month('2023-03-31T23:59:59')->format('Y-m'));
    }

    public function testNextMonth(): void
    {
        self::assertSame('2023-04', next_month('2023-03')->format('Y-m'));
        self::assertSame('2023-01', next_month('2022-12-31T23:59:59')->format('Y-m'));
        self::assertSame('2023-04', next_month('2023-03-31T23:59:59')->format('Y-m'));
    }

    /**
     * @return array<array<mixed>>
     */
    public function isSameDayDataProvider(): array
    {
        return [
            ['2023-01-01', '2023-01-01', true],
            ['2023-01-01T12:13:14', '2023-01-01 00:00:00', true],
            ['2023-01-01T12:13:14', '2023-01-01', true],
            ['2023-01-01', '2023-01-01 00:00:00', true],
            [new \DateTimeImmutable('2023-01-01'), '2023-01-01', true],
            ['2023-01-01', new \DateTimeImmutable('2023-01-01'), true],
            [new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-01-01'), true],

            ['2023-01-01', '2023-01-02', false],
            ['2023-02-01T12:13:14', '2023-01-01 00:00:00', false],
            ['2023-01-01T12:13:14', '1988-01-01', false],
            ['2023-01-01', '2023-01-02 00:00:00', false],
            [new \DateTimeImmutable('2023-02-01'), '2023-01-01', false],
            ['2023-02-01', new \DateTimeImmutable('2023-01-01'), false],
        ];
    }
}
