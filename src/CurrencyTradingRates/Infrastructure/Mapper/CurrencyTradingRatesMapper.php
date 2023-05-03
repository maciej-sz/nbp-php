<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;

class CurrencyTradingRatesMapper
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
     * @param array<array<mixed>> $ratesData
     * @return array<CurrencyTradingRate>
     */
    public function rawDataToDomainObjectCollection(array $tableData, array $ratesData): array
    {
        $collection = [];
        foreach ($ratesData as $rateData) {
            $currencyTradingRate = $this->rawDataToDomainObject($tableData, $rateData);
            $collection[$currencyTradingRate->getCurrencyCode()] = $currencyTradingRate;
        }

        return $collection;
    }

    /**
     * @param array<mixed> $tableData
     * @param array<mixed> $ratesData
     */
    public function rawDataToDomainObject(array $tableData, array $ratesData): CurrencyTradingRate
    {
        $tableDataAccess = new ArrayDataAccess($tableData);
        $ratesDataAccess = new ArrayDataAccess($ratesData);

        $currencyName = $ratesDataAccess->extractString('currency');
        $currencyCode = $ratesDataAccess->extractString('code');
        $bid = $ratesDataAccess->extractFloat('bid');
        $ask = $ratesDataAccess->extractFloat('ask');
        $tradingDate = $tableDataAccess->extractDateTime('tradingDate');
        $effectiveDate = $tableDataAccess->extractDateTime('effectiveDate');

        $this->rateValidator->validate($bid);
        $this->rateValidator->validate($ask);

        return new CurrencyTradingRate(
            $currencyName,
            $currencyCode,
            $bid,
            $ask,
            $tradingDate,
            $effectiveDate
        );
    }
}
