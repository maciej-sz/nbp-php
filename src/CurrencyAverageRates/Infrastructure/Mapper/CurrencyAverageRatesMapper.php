<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\Shared\Domain\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Domain\Validator\ThrowableValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;

class CurrencyAverageRatesMapper
{
    /** @var ThrowableValidator<mixed> */
    private $rateValidator;

    /**
     * @param ?ThrowableValidator<mixed> $rateValidator
     */
    public function __construct(?ThrowableValidator $rateValidator = null)
    {
        if (null === $rateValidator) {
            $rateValidator = new NbpNumericRateValidator();
        }
        $this->rateValidator = $rateValidator;
    }

    /**
     * @param array{table: string, no: string, effectiveDate: string, rates: array<mixed>} $tableData
     * @param array<array{currency: string, code: string, mid: float}> $rates
     * @return array<CurrencyAverageRate>
     */
    public function rawDataToDomainObjectCollection(array $tableData, array $rates): array
    {
        $collection = [];
        foreach ($rates as $rateData) {
            $collection[] = $this->rawDataToDomainObject($tableData, $rateData);
        }

        return $collection;
    }

    /**
     * @param array{table: string, no: string, effectiveDate: string, rates: array<mixed>} $tableData
     * @param array{currency: string, code: string, mid: float} $rateData
     */
    public function rawDataToDomainObject(array $tableData, array $rateData): CurrencyAverageRate
    {
        $tableDataAccess = new ArrayDataAccess($tableData);
        $rateDataAccess = new ArrayDataAccess($rateData);

        $currencyName = $rateDataAccess->extractString('currency');
        $currencyCode = $rateDataAccess->extractString('code');
        $avg = $rateDataAccess->extractFloat('mid');
        $effectiveDate = $tableDataAccess->extractDateTime('effectiveDate');

        $this->rateValidator->validate($avg);

        return new CurrencyAverageRate(
            $currencyName,
            $currencyCode,
            $avg,
            $effectiveDate
        );
    }
}
