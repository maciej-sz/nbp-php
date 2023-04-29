<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;

class CurrencyAverageRatesMapper
{
    /** @var ThrowableValidator<mixed> */
    private $rateValidator;

    /**
     * @param ThrowableValidator<mixed>|null $rateValidator
     */
    public function __construct(?ThrowableValidator $rateValidator = null)
    {
        if (null === $rateValidator) {
            $rateValidator = new NbpNumericRateValidator();
        }
        $this->rateValidator = $rateValidator;
    }

    /**
     * @param array<mixed> $tableData
     * @param array<mixed> $ratesData
     * @return array<CurrencyAverageRate>
     */
    public function rawDataToDomainObjectCollection(array $tableData, array $ratesData): array
    {
        $collection = [];
        foreach ($ratesData as $rateData) {
            $collection[] = $this->rawDataToDomainObject($tableData, $rateData);
        }

        return $collection;
    }

    /**
     * @param array<mixed> $tableData
     * @param array<mixed> $rateData
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
