<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Repository;

use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Service\NbpCache;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpClient;

class NbpRepositoryFactory
{
    /** @var CurrencyAveragesTableMapper */
    private $currencyAveragesTableMapper;
    /** @var CurrencyTradingTableMapper */
    private $currencyTradingTableMapper;
    /** @var GoldRatesMapper */
    private $goldRatesMapper;

    public function __construct(
        ?CurrencyAveragesTableMapper $currencyAveragesTableMapper = null,
        ?CurrencyTradingTableMapper $currencyTradingTableMapper = null,
        ?GoldRatesMapper $goldRatesMapper = null
    ) {
        $this->currencyAveragesTableMapper = $currencyAveragesTableMapper;
        $this->currencyTradingTableMapper = $currencyTradingTableMapper;
        $this->goldRatesMapper = $goldRatesMapper;
    }

    public function create(NbpClient $client, ?NbpCache $cache = null)
    {
    }
}
