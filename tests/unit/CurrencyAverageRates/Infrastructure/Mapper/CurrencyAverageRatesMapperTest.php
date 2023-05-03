<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyAverageRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyAverageRates\Domain\CurrencyAverageRate;
use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAverageRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class CurrencyAverageRatesMapperTest extends TestCase
{
    public function testRawDataToDomainObject(): void
    {
        $mapper = new CurrencyAverageRatesMapper();
        $tables = FixturesRepository::create()->fetchAverageTablesJson('A', '2023-03-01', '2023-03-02');
        $rate = $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);

        self::assertSame('USD', $rate->getCurrencyCode());
        self::assertSame('dolar amerykański', $rate->getCurrencyName());
        self::assertSame('2023-03-01T00:00:00+01:00', $rate->getEffectiveDate()->format('c'));
        self::assertSame(4.4, $rate->getValue());
    }

    public function testRawDataToDomainObjectCollection(): void
    {
        $mapper = new CurrencyAverageRatesMapper();
        $tables = FixturesRepository::create()->fetchAverageTablesJson('A', '2023-03-01', '2023-03-02');
        $rates = $mapper->rawDataToDomainObjectCollection($tables[0], $tables[0]['rates']);

        self::assertCount(3, $rates);
        self::assertContainsOnly(CurrencyAverageRate::class, $rates);
        self::assertSame('USD', $rates[0]->getCurrencyCode());
        self::assertSame('EUR', $rates[1]->getCurrencyCode());
        self::assertSame('CHF', $rates[2]->getCurrencyCode());
    }

    public function testValidatorFailure(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid rate: 4.4');

        $mockValidator = new class implements ThrowableValidator {
            public function validate($value): void
            {
                assert(is_float($value));
                throw new ValidationException("Invalid rate: {$value}");
            }
        };
        $tables = FixturesRepository::create()->fetchAverageTablesJson('A', '2023-03-01', '2023-03-02');

        $mapper = new CurrencyAverageRatesMapper($mockValidator);
        $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);
    }
}
