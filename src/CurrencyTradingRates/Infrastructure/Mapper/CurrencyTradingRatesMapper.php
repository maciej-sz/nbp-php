<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRates;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Validator;

class CurrencyTradingRatesMapper
{
    /** @var Validator<mixed> */
    private $rateValidator;

    /**
     * @param Validator<mixed>|null $rateValidator
     */
    public function __construct(?Validator $rateValidator = null)
    {
        if (null === $rateValidator) {
            $rateValidator = new NbpNumericRateValidator();
        }
        $this->rateValidator = $rateValidator;
    }

    /**
     * @param array<mixed> $tableData
     * @param array<mixed> $ratesData
     * @return array<CurrencyTradingRates>
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
     * @param array<mixed> $ratesData
     */
    public function rawDataToDomainObject(array $tableData, array $ratesData): CurrencyTradingRates
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

        return new CurrencyTradingRates(
            $currencyName,
            $currencyCode,
            $bid,
            $ask,
            $tradingDate,
            $effectiveDate
        );
    }
}
