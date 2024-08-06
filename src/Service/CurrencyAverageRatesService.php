<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Service;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection\RatesFlatCollection;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Collection\TablesDictionary;
use MaciejSz\Nbp\Shared\Domain\Exception\NoDataException;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;

use function MaciejSz\Nbp\Shared\Domain\DateFormatter\ensure_date_obj;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\ensure_mutable_date_obj;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\extract_ym;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\is_same_day;
use function MaciejSz\Nbp\Shared\Domain\DateFormatter\previous_month;

class CurrencyAverageRatesService
{
    /** @var NbpRepository */
    private $nbpRepository;

    public function __construct(NbpRepository $nbpRepository)
    {
        $this->nbpRepository = $nbpRepository;
    }

    public static function new(?NbpRepository $nbpRepository = null): self
    {
        if (null === $nbpRepository) {
            $nbpRepository = NbpWebRepository::new();
        }

        return new self($nbpRepository);
    }

    public function fromMonth(int $year, int $month): RatesFlatCollection
    {
        return new RatesFlatCollection(
            $this->getMonthTablesA($year, $month),
            $this->getMonthTablesB($year, $month)
        );
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromDay($date): TablesDictionary
    {
        $date = ensure_date_obj($date);
        $tableA = $this->findTableFromDate($date, $this->getMonthTablesA(...extract_ym($date)));
        $tableB = $this->findTableFromDate($date, $this->getMonthTablesB(...extract_ym($date)));

        $tables = [];
        if (null !== $tableA) {
            $tables[CurrencyAveragesTable::A] = $tableA;
        }
        if (null !== $tableB) {
            $tables[CurrencyAveragesTable::B] = $tableB;
        }

        return new TablesDictionary($tables);
    }

    /**
     * @param string|\DateTimeInterface $date
     */
    public function fromDayBefore($date): TablesDictionary
    {
        $date = ensure_date_obj($date);
        $monthTablesA = $this->getMonthTablesA(...extract_ym($date));
        $tableA = $this->findTableFromDateBefore($date, $monthTablesA);
        if (null === $tableA) {
            $previousMonthTablesA = $this->getMonthTablesA(...extract_ym(previous_month($date)));
            $tableA = $this->getLastTable($previousMonthTablesA);
        }

        $monthTablesB = $this->getMonthTablesB(...extract_ym($date));
        $tableB = $this->findTableFromDateBefore($date, $monthTablesB);
        if (null === $tableB) {
            $previousMonthTablesB = $this->getMonthTablesB(...extract_ym(previous_month($date)));
            $tableB = $this->getLastTable($previousMonthTablesB);
        }

        $tables = [];
        if (null !== $tableA) {
            $tables[CurrencyAveragesTable::A] = $tableA;
        }
        if (null !== $tableB) {
            $tables[CurrencyAveragesTable::B] = $tableB;
        }

        return new TablesDictionary($tables);
    }

    /**
     * @return iterable<CurrencyAveragesTable>
     */
    public function getMonthTablesA(int $year, int $month): iterable
    {
        yield from $this->nbpRepository->getCurrencyAveragesTableA($year, $month);
    }

    /**
     * @return iterable<CurrencyAveragesTable>
     */
    public function getMonthTablesB(int $year, int $month): iterable
    {
        yield from $this->nbpRepository->getCurrencyAveragesTableB($year, $month);
    }

    /**
     * @param string|\DateTimeInterface $date
     * @param iterable<CurrencyAveragesTable> $tables
     */
    private function findTableFromDate($date, iterable $tables): ?CurrencyAveragesTable
    {
        $date = ensure_date_obj($date);
        try {
            foreach ($tables as $table) {
                if (is_same_day($date, $table->getEffectiveDate())) {
                    return $table;
                }
            }
        } catch (NoDataException $e) {
            return null;
        }

        return null;
    }

    /**
     * @param string|\DateTimeInterface $date
     * @param iterable<CurrencyAveragesTable> $tables
     */
    private function findTableFromDateBefore($date, iterable $tables): ?CurrencyAveragesTable
    {
        $date = ensure_date_obj($date);
        try {
            $prevTable = null;
            foreach ($tables as $table) {
                $nextTableDay = ensure_mutable_date_obj($table->getEffectiveDate())->modify('+1 day');

                if (is_same_day($date, $nextTableDay)) {
                    return $table;
                }
                if (is_same_day($date, $table->getEffectiveDate())) {
                    return $prevTable;
                }
                $prevTable = $table;
            }
        } catch (NoDataException $e) {
            return null;
        }

        return null;
    }

    /**
     * @param iterable<CurrencyAveragesTable> $tables
     */
    private function getLastTable(iterable $tables): ?CurrencyAveragesTable
    {
        $last = null;
        try {
            foreach ($tables as $table) {
                $last = $table;
            }
        } catch (NoDataException $e) {
            return null;
        }

        return $last;
    }
}
