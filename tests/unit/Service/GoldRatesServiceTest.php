<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Service;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\RateNotFoundException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use PHPUnit\Framework\TestCase;

class GoldRatesServiceTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        $service = GoldRatesService::new();
        self::assertInstanceOf(GoldRatesService::class, $service);
    }

    public function testFromMonth(): void
    {
        $rate = $this->createMock(GoldRate::class);
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getGoldRates')
            ->with(2023, 3)
            ->willReturn([$rate])
        ;
        self::assertSame(
            [$rate],
            (new GoldRatesService($repository))->fromMonth(2023, 3)->toArray()
        );
    }

    public function testFromDay(): void
    {
        $rate1 = $this->createMock(GoldRate::class);
        $rate1
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-03-01'))
        ;

        $rate2 = $this->createMock(GoldRate::class);
        $rate2
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-03-02'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getGoldRates')
            ->with(2023, 3)
            ->willReturn([$rate1, $rate2])
        ;

        $service = new GoldRatesService($repository);
        self::assertSame($rate2, $service->fromDay('2023-03-02'));
    }

    public function testFromDayNotFound(): void
    {
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getGoldRates')
            ->with(2023, 3)
            ->willReturn([])
        ;

        $service = new GoldRatesService($repository);

        self::expectException(RateNotFoundException::class);
        self::expectExceptionMessage('Gold rate from 2023-03-01 has not been found');

        $service->fromDay('2023-03-01');
    }

    public function testFromDayBeforeCurrentMonth(): void
    {
        $rate1 = $this->createMock(GoldRate::class);
        $rate1
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-03-01'))
        ;

        $rate2 = $this->createMock(GoldRate::class);
        $rate2
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-03-02'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::once())
            ->method('getGoldRates')
            ->with(2023, 3)
            ->willReturn([$rate1, $rate2])
        ;

        $service = new GoldRatesService($repository);
        self::assertSame($rate1, $service->fromDayBefore('2023-03-02'));
    }

    public function testFromDayBeforePreviousMonth(): void
    {
        $rate1 = $this->createStub(GoldRate::class);

        $rate2 = $this->createMock(GoldRate::class);
        $rate2
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-03-01'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getGoldRates')
            ->willReturnMap([
                [2023, 2, [$rate1]],
                [2023, 3, [$rate2]],
            ])
        ;

        $service = new GoldRatesService($repository);
        self::assertSame($rate1, $service->fromDayBefore('2023-03-01'));
    }

    public function testFromDayBeforeNotFound(): void
    {
        $rate = $this->createMock(GoldRate::class);
        $rate
            ->expects(self::once())
            ->method('getDate')
            ->willReturn(new \DateTimeImmutable('2023-02-01'))
        ;

        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getGoldRates')
            ->willReturnMap([
                [2023, 1, []],
                [2023, 2, [$rate]],
            ])
        ;

        $service = new GoldRatesService($repository);

        self::expectException(RateNotFoundException::class);
        self::expectExceptionMessage('Gold rate from day before 2023-02-01 has not been found');

        $service->fromDayBefore('2023-02-01');
    }

    public function testFromDayBeforeNotFoundInEmptyMonth(): void
    {
        $repository = $this->createMock(NbpRepository::class);
        $repository
            ->expects(self::exactly(2))
            ->method('getGoldRates')
            ->willReturn([])
        ;

        $service = new GoldRatesService($repository);

        self::expectException(RateNotFoundException::class);
        self::expectExceptionMessage('Gold rate from day before 2023-03-15 has not been found');

        $service->fromDayBefore('2023-03-15');
    }
}
