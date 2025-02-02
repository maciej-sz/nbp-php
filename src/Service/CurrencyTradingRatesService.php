<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Service;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Collection\RatesFlatCollection;
use MaciejSz\Nbp\Shared\Domain\Exception\InvalidDateException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;

use function MaciejSz\Nbp\Shared\Domain\DateFormatter\ensure_date_obj;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\extract_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\is_same_day;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\next_month;

class CurrencyTradingRatesService
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

    public function fromMonth(int $year, int $month): RatesFlatCollection
    {
        return new RatesFlatCollection($this->getMonthTables($year, $month));
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromEffectiveDay($date): CurrencyTradingTable
    {
        $date = ensure_date_obj($date);
        $tables = $this->getMonthTables(...extract_ym($date));
        foreach ($tables as $table) {
            if (is_same_day($date, $table->getEffectiveDate())) {
                return $table;
            }
        }
        throw new InvalidDateException(
            "Rates table for effective date {$date->format('Y-m-d')} is not available"
        );
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromTradingDay($date): CurrencyTradingTable
    {
        $date = ensure_date_obj($date);
        $table = $this->findTableByTradingDate($date);
        if (!$table) {
            $table = $this->findTableByTradingDate($date, next_month($date));
        }

        if (!$table) {
            throw new InvalidDateException(
                "Rates table for trading date {$date->format('Y-m-d')} is not available"
            );
        }

        return $table;
    }

    /**
     * @return iterable<CurrencyTradingTable>
     */
    public function getMonthTables(int $year, int $month): iterable
    {
        yield from $this->nbpRepository->getCurrencyTradingTables($year, $month);
    }

    private function findTableByTradingDate(
        \DateTimeInterface $tradingDate,
        ?\DateTimeInterface $searchMonth = null,
    ): ?CurrencyTradingTable {
        if (null === $searchMonth) {
            $searchMonth = $tradingDate;
        }
        $tables = $this->getMonthTables(...extract_ym($searchMonth));
        foreach ($tables as $table) {
            if (is_same_day($tradingDate, $table->getTradingDate())) {
                return $table;
            }
        }

        return null;
    }
}
