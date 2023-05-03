<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Service;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\GoldRates\Infrastructure\Collection\GoldRatesCollection;
use MaciejSz\Nbp\Shared\Domain\Exception\RateNotFoundException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\ensure_date_obj;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\extract_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\is_same_day;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\previous_month;

class GoldRatesService
{
    /** @var NbpRepository */
    private $nbpRepository;

    public function __construct(NbpRepository $nbpRepository)
    {
        $this->nbpRepository = $nbpRepository;
    }

    public static function create(?NbpRepository $nbpRepository = null): self
    {
        if (null === $nbpRepository) {
            $nbpRepository = NbpWebRepository::create();
        }

        return new self($nbpRepository);
    }

    /**
     * @return GoldRatesCollection
     */
    public function fromMonth(int $year, int $month): GoldRatesCollection
    {
        return new GoldRatesCollection($this->nbpRepository->getGoldRates($year, $month));
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromDay($date): GoldRate
    {
        $date = ensure_date_obj($date);
        $rates = $this->fromMonth(...extract_ym($date));
        foreach ($rates as $rate) {
            if (is_same_day($rate->getDate(), $date)) {
                return $rate;
            }
        }

        throw new RateNotFoundException(
            "Gold rate from {$date->format('Y-m-d')} has not been found"
        );
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromDayBefore($date): GoldRate
    {
        $date = ensure_date_obj($date);
        $monthRates = $this->fromMonth(...extract_ym($date));
        $rate = $this->findRateBefore($date, $monthRates);
        if (null === $rate) {
            $previousMonthRates = $this->fromMonth(...extract_ym(previous_month($date)));
            $rate = $this->getLastRate($previousMonthRates);
        }

        if (null === $rate) {
            throw new RateNotFoundException(
                "Gold rate from day before {$date->format('Y-m-d')} has not been found"
            );
        }

        return $rate;
    }

    /**
     * @param iterable<GoldRate> $rates
     */
    private function findRateBefore($date, iterable $rates): ?GoldRate
    {
        $previousRate = null;
        foreach ($rates as $rate) {
            if (is_same_day($date, $rate->getDate())) {
                return $previousRate;
            }
            $previousRate = $rate;
        }

        return null;
    }

    public function getLastRate(iterable $rates): ?GoldRate
    {
        $last = null;
        foreach ($rates as $rate) {
            $last = $rate;
        }

        return $last;
    }
}
