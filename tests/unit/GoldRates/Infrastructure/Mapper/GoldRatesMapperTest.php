<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\GoldRates\Infrastructure\Mapper;

use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\ThrowableValidator;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class GoldRatesMapperTest extends TestCase
{
    public function testRawDataToDomainObject(): void
    {
        $mapper = new GoldRatesMapper();
        $tables = $this->fetchFixtureTables();
        $rate = $mapper->rawDataToDomainObject($tables[0]);

        self::assertSame('2023-03-01', $rate->getDate()->format('Y-m-d'));
        self::assertSame(260.89, $rate->getValue());
    }

    public function testFailingValidator(): void
    {
        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Invalid value: 260.89');

        $mockValidator = new class implements ThrowableValidator {
            public function validate($value): void
            {
                assert(is_float($value));
                throw new ValidationException("Invalid value: {$value}");
            }
        };
        $mapper = new GoldRatesMapper($mockValidator);
        $tables = $this->fetchFixtureTables();
        $mapper->rawDataToDomainObject($tables[0]);
    }

    /**
     * @return array<array{data: string, cena: float}>
     */
    private function fetchFixtureTables(): array
    {
        $fixturesRepository = new FixturesRepository();

        // @phpstan-ignore-next-line
        return $fixturesRepository->fetchArray('/api/cenyzlota/2023-03-01/2023-03-03/data');;
    }
}
