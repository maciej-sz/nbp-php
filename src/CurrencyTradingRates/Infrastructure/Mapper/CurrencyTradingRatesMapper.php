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
     * @param array{
     *     table: string,
     *     no: string,
     *     tradingDate: string,
     *     effectiveDate: string,
     *     rates: array<mixed>,
     * } $tableData
     * @param array<
     *     array{
     *         currency: string,
     *         code: string,
     *         bid: float,
     *         ask: float,
     *     }
     * > $rates
     * @return array<CurrencyTradingRate>
     */
    public function rawDataToDomainObjectCollection(array $tableData, array $rates): array
    {
        $collection = [];
        foreach ($rates as $rate) {
            $currencyTradingRate = $this->rawDataToDomainObject($tableData, $rate);
            $collection[$currencyTradingRate->getCurrencyCode()] = $currencyTradingRate;
        }

        return $collection;
    }

    /**
     * @param array{
     *     table: string,
     *     no: string,
     *     tradingDate: string,
     *     effectiveDate: string,
     *     rates: array<mixed>,
     * } $tableData
     * @param array{
     *     currency: string,
     *     code: string,
     *     bid: float,
     *     ask: float,
     * } $ratesData
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
