<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Domain;

use PHPUnit\Framework\TestCase;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\first_day_of_month;

class DateFormatterTest extends TestCase
{
    public function testFirstDayOfMonth()
    {
        $this->assertSame('2022-01-01', first_day_of_month(2022, 1));
        $this->assertSame('1234-12-01', first_day_of_month(1234, 12));
    }
}
