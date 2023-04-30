<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRates;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRatesMapperTest extends TestCase
{
    public function testRawDataToDomainObject()
    {
        $mapper = new CurrencyTradingRatesMapper();
        $tables = $this->fetchFixtureTables();
        $rate = $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);

        self::assertSame('USD', $rate->getCurrencyCode());
        self::assertSame('dolar amerykański', $rate->getCurrencyName());
        self::assertSame('2023-02-28T00:00:00+01:00', $rate->getTradingDate()->format('c'));
        self::assertSame('2023-03-01T00:00:00+01:00', $rate->getEffectiveDate()->format('c'));
        self::assertSame(4.3, $rate->getBid());
        self::assertSame(4.4, $rate->getAsk());
    }

    public function testRawDataToDomainObjectCollection()
    {
        $mapper = new CurrencyTradingRatesMapper();
        $tables = $this->fetchFixtureTables();
        $rates = $mapper->rawDataToDomainObjectCollection($tables[0], $tables[0]['rates']);

        self::assertCount(3, $rates);
        self::assertContainsOnly(CurrencyTradingRates::class, $rates);
        self::assertSame('USD', $rates[0]->getCurrencyCode());
        self::assertSame('EUR', $rates[1]->getCurrencyCode());
        self::assertSame('CHF', $rates[2]->getCurrencyCode());
    }

    public function testValidatorFailure()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid rate: 4.3');

        $mockValidator = new class implements ThrowableValidator {
            public function validate($value): void
            {
                throw new ValidationException("Invalid rate: {$value}");
            }
        };

        $tables = $this->fetchFixtureTables();
        $mapper = new CurrencyTradingRatesMapper($mockValidator);
        $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);
    }

    /**
     * @return array<mixed>
     */
    private function fetchFixtureTables(): array
    {
        $fixturesRepository = new FixturesRepository();

        return $fixturesRepository->fetchArray('/api/exchangerates/tables/C/2023-03-01/2023-03-02/data');
    }
}
