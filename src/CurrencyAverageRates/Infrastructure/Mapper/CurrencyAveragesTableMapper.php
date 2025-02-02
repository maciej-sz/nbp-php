<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAveragesTable;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;

/**
 * @implements TableMapper<CurrencyAveragesTable>
 */
class CurrencyAveragesTableMapper implements TableMapper
{
    /** @var CurrencyAverageRatesMapper */
    private $ratesMapper;

    public function __construct(?CurrencyAverageRatesMapper $ratesMapper = null)
    {
        if (null === $ratesMapper) {
            $ratesMapper = new CurrencyAverageRatesMapper();
        }
        $this->ratesMapper = $ratesMapper;
    }

    /**
     * @param array{table: string, no: string, effectiveDate: string, rates: array<array-key, mixed>} $tableData
     */
    public function rawDataToDomainObject(array $tableData): CurrencyAveragesTable
    {
        $dataAccess = new ArrayDataAccess($tableData);
        $tableLetter = $dataAccess->extractString('table');
        $tableNumber = $dataAccess->extractString('no');
        $effectiveDate = $dataAccess->extractDateTime('effectiveDate');
        /** @var array<array{currency: string, code: string, mid: float}> $rates */
        $rates = $dataAccess->extractArray('rates');

        return new CurrencyAveragesTable(
            $tableLetter,
            $tableNumber,
            $effectiveDate,
            $this->ratesMapper->rawDataToDomainObjectCollection($tableData, $rates)
        );
    }
}
