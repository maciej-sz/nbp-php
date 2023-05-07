<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Repository;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\GoldRates\Domain\GoldRate;

interface NbpRepository
{
    /**
     * @return iterable<CurrencyAveragesTable>
     */
    public function getCurrencyAveragesTableA(int $year, int $month): iterable;

    /**
     * @return iterable<CurrencyAveragesTable>
     */
    public function getCurrencyAveragesTableB(int $year, int $month): iterable;

    /**
     * @return iterable<CurrencyTradingTable>
     */
    public function getCurrencyTradingTables(int $year, int $month): iterable;

    /**
     * @return iterable<GoldRate>
     */
    public function getGoldRates(int $year, int $month): iterable;
}
