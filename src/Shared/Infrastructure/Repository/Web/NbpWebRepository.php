<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Repository\Web;

use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Service\NbpCache;
use MaciejSz\Nbp\Shared\Domain\DateFormatter;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpRepository;

final class NbpWebRepository implements NbpRepository
{
    /** @var NbpClient */
    private $client;
    /** @var NbpCache|null */
    private $nbpCache;
    /** @var CurrencyAveragesTableMapper */
    private $currencyAveragesTableMapper;
    /** @var CurrencyTradingTableMapper */
    private $currencyTradingTableMapper;
    /** @var GoldRatesMapper */
    private $goldRatesMapper;

    public function __construct(
        NbpClient $client,
        ?NbpCache $nbpCache = null,
        ?CurrencyAveragesTableMapper $currencyAveragesTableMapper = null,
        ?CurrencyTradingTableMapper $currencyTradingTableMapper = null,
        ?GoldRatesMapper $goldRatesMapper = null
    ) {
        if (null === $currencyAveragesTableMapper) {
            $currencyAveragesTableMapper = new CurrencyAveragesTableMapper();
        }
        if (null === $currencyTradingTableMapper) {
            $currencyTradingTableMapper = new CurrencyTradingTableMapper();
        }
        if (null === $goldRatesMapper) {
            $goldRatesMapper = new GoldRatesMapper();
        }
        $this->client = $client;
        $this->nbpCache = $nbpCache;
        $this->currencyAveragesTableMapper = $currencyAveragesTableMapper;
        $this->currencyTradingTableMapper = $currencyTradingTableMapper;
        $this->goldRatesMapper = $goldRatesMapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyAveragesTableA(int $year, int $month): iterable
    {
        // TODO: Implement getCurrencyAveragesTableA() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyAveragesTableB(int $year, int $month): iterable
    {
        // TODO: Implement getCurrencyAveragesTableB() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyTradingTables(int $year, int $month): iterable
    {
        $dateFormatter = new DateFormatter();

        $startDate = $dateFormatter->firstDayOfMonth($year, $month);
        $endDate = $dateFormatter->lastDayOfMonth($year, $month);

        $tradingTables = $this->client->getCurrencyTradingTables($startDate, $endDate);
        foreach ($tradingTables as $tradingTableData) {
            yield $this->currencyTradingTableMapper->mapRawDataToDomainObject($tradingTableData);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getGoldRates(int $year, int $month): iterable
    {
        // TODO: Implement getGoldRates() method.
    }

    private function getTradingTablesHydrator(array $tradingTables): \Generator
    {
        // TODO:
    }
}
