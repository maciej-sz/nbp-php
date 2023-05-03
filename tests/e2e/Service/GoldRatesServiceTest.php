<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\E2e\Service;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\RateNotFoundException;
use PHPUnit\Framework\TestCase;

class GoldRatesServiceTest extends TestCase
{
    public function testForMonth(): void
    {
        $goldRates = GoldRatesService::create();
        $jan2013rates = $goldRates->fromMonth(2013, 1)->toArray();

        self::assertContainsOnly(GoldRate::class, $jan2013rates);
        self::assertCount(22, $jan2013rates);
    }

    public function testFromDay(): void
    {
        $goldRates = GoldRatesService::create();
        $febFourth2013Rate = $goldRates->fromDay('2013-02-04');

        self::assertSame('2013-02-04', $febFourth2013Rate->getDate()->format('Y-m-d'));
        self::assertSame(164.97, $febFourth2013Rate->getRate());
    }

    public function testFromDayInvalid(): void
    {
        self::expectException(RateNotFoundException::class);
        self::expectExceptionMessage('Gold rate from 2013-02-03 has not been found');

        $goldRates = GoldRatesService::create();
        $goldRates->fromDay('2013-02-03');
    }

    public function testFromDayBefore(): void
    {
        $goldRates = GoldRatesService::create();
        $febFourth2013Rate = $goldRates->fromDayBefore('2013-02-04');

        self::assertSame('2013-02-01', $febFourth2013Rate->getDate()->format('Y-m-d'));
        self::assertSame(165.24, $febFourth2013Rate->getRate());
    }

    public function testFromDayBeforeNewYear(): void
    {
        $goldRates = GoldRatesService::create();
        $febFourth2013Rate = $goldRates->fromDayBefore('2014-01-02');

        self::assertSame('2013-12-31', $febFourth2013Rate->getDate()->format('Y-m-d'));
        self::assertSame(116.89, $febFourth2013Rate->getRate());
    }
}
