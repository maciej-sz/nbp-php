<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\CurrencyTradingRates\Infrastructure\Mapper;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class CurrencyTradingRatesMapperTest extends TestCase
{
    public function testRawDataToDomainObject(): void
    {
        $mapper = new CurrencyTradingRatesMapper();
        $tables = FixturesRepository::create()->fetchTradingTablesJson('2023-03-01', '2023-03-02');
        $rate = $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);

        self::assertSame('USD', $rate->getCurrencyCode());
        self::assertSame('dolar amerykański', $rate->getCurrencyName());
        self::assertSame('2023-02-28T00:00:00+01:00', $rate->getTradingDate()->format('c'));
        self::assertSame('2023-03-01T00:00:00+01:00', $rate->getEffectiveDate()->format('c'));
        self::assertSame(4.3, $rate->getBid());
        self::assertSame(4.4, $rate->getAsk());
    }

    public function testRawDataToDomainObjectCollection(): void
    {
        $mapper = new CurrencyTradingRatesMapper();
        $tables = FixturesRepository::create()->fetchTradingTablesJson('2023-03-01', '2023-03-02');
        $rates = $mapper->rawDataToDomainObjectCollection($tables[0], $tables[0]['rates']);

        self::assertCount(3, $rates);
        self::assertContainsOnly(CurrencyTradingRate::class, $rates);
        self::assertEquals(['USD', 'EUR', 'CHF'], array_keys($rates));
        self::assertSame(4.3, $rates['USD']->getBid());
        self::assertSame(4.6, $rates['EUR']->getBid());
        self::assertSame(4.8, $rates['CHF']->getBid());
    }

    public function testValidatorFailure(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid rate: 4.3');

        $mockValidator = new class() implements ThrowableValidator {
            public function validate($value): void
            {
                assert(is_float($value));
                throw new ValidationException("Invalid rate: {$value}");
            }
        };

        $tables = FixturesRepository::create()->fetchTradingTablesJson('2023-03-01', '2023-03-02');
        $mapper = new CurrencyTradingRatesMapper($mockValidator);
        $mapper->rawDataToDomainObject($tables[0], $tables[0]['rates'][0]);
    }
}
