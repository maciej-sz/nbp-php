<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\GoldRates\Infrastructure\Mapper;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Domain\Validator\NbpNumericRateValidator;
use MaciejSz\Nbp\Shared\Domain\Validator\ThrowableValidator;

/**
 * @implements TableMapper<GoldRate>
 */
class GoldRatesMapper implements TableMapper
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
     * @param array{data: string, cena: float} $goldRate
     * @return GoldRate
     */
    public function rawDataToDomainObject(array $goldRate): object
    {
        $rateDataAccess = new ArrayDataAccess($goldRate);
        $date = $rateDataAccess->extractDateTime('data');
        $rate = $rateDataAccess->extractFloat('cena');

        $this->rateValidator->validate($rate);

        return new GoldRate($date, $rate);
    }
}
