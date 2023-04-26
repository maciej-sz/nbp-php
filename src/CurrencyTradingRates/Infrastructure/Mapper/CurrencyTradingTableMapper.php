<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;

class CurrencyTradingTableMapper
{
    /** @var CurrencyTradingRatesMapper */
    private $ratesMapper;

    public function __construct(?CurrencyTradingRatesMapper $ratesMapper = null)
    {
        if (null === $ratesMapper) {
            $ratesMapper = new CurrencyTradingRatesMapper();
        }
        $this->ratesMapper = $ratesMapper;
    }

    /**
     * @param array<mixed> $tableData
     */
    public function mapRawDataToDomainObject(array $tableData): CurrencyTradingTable
    {
        $dataAccess = new ArrayDataAccess($tableData);

        $tableLetter = $dataAccess->extractString('table');
        $tableNumber = $dataAccess->extractString('no');
        $tradingDate = $dataAccess->extractDateTime('tradingDate');
        $effectiveDate = $dataAccess->extractDateTime('effectiveDate');
        $rates = $dataAccess->extractArray('rates');

        return new CurrencyTradingTable(
            $tableLetter,
            $tableNumber,
            $tradingDate,
            $effectiveDate,
            $this->ratesMapper->rawDataToDomainObjectCollection($tableData, $rates)
        );
    }
}
