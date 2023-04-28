<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Infrastructure\Mapper;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Validator;

/**
 * @implements TableMapper<array<GoldRate>>
 */
class GoldRatesMapper implements TableMapper
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
     * @param array<mixed> $goldRates
     * @return array<GoldRate>
     */
    public function rawDataToDomainObject(array $goldRates): array
    {
        $domainObjects = [];

        foreach ($goldRates as $goldRate) {
            $rateDataAccess = new ArrayDataAccess($goldRate);
            $date = $rateDataAccess->extractDateTime('date');
            $rate = $rateDataAccess->extractFloat('cena');

            $this->rateValidator->validate($rate);

            $domainObjects[] = new GoldRate($date, $rate);
        }

        return $domainObjects;
    }
}
