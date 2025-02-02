<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;

/**
 * @implements TableMapper<CurrencyTradingTable>
 */
class CurrencyTradingTableMapper implements TableMapper
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
     * @param array{
     *     table: string,
     *     no: string,
     *     tradingDate: string,
     *     effectiveDate: string,
     *     rates: array<array-key, mixed>,
     * } $tableData
     */
    public function rawDataToDomainObject(array $tableData): CurrencyTradingTable
    {
        $dataAccess = new ArrayDataAccess($tableData);

        $tableLetter = $dataAccess->extractString('table');
        $tableNumber = $dataAccess->extractString('no');
        $tradingDate = $dataAccess->extractDateTime('tradingDate');
        $effectiveDate = $dataAccess->extractDateTime('effectiveDate');
        /**
         * @var array<
         *     array{
         *         currency: string,
         *         code: string,
         *         bid: float,
         *         ask: float,
         *     }
         * > $rates
         */
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
