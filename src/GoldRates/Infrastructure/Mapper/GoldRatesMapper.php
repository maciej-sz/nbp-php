<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Infrastructure\Mapper;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;

/**
 * @implements TableMapper<array<GoldRate>>
 */
class GoldRatesMapper implements TableMapper
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
     * @param array<mixed> $goldRates
     * @return array<string, GoldRate>
     */
    public function rawDataToDomainObject(array $goldRates): array
    {
        $domainObjects = [];

        foreach ($goldRates as $goldRate) {
            $rateDataAccess = new ArrayDataAccess($goldRate);
            $date = $rateDataAccess->extractDateTime('data');
            $rate = $rateDataAccess->extractFloat('cena');
            $dateKey = $date->format('Y-m-d');

            $this->rateValidator->validate($rate);

            $domainObjects[$dateKey] = new GoldRate($date, $rate);
        }

        return $domainObjects;
    }
}
