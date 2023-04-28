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
     * @param array<mixed> $tableData
     */
    public function rawDataToDomainObject(array $tableData): CurrencyAveragesTable
    {
        $dataAccess = new ArrayDataAccess($tableData);
        $tableLetter = $dataAccess->extractString('table');
        $tableNumber = $dataAccess->extractString('no');
        $effectiveDate = $dataAccess->extractDateTime('effectiveDate');
        $rates = $dataAccess->extractArray('rates');

        return new CurrencyAveragesTable(
            $tableLetter,
            $tableNumber,
            $effectiveDate,
            $this->ratesMapper->rawDataToDomainObjectCollection($tableData, $rates)
        );
    }
}
