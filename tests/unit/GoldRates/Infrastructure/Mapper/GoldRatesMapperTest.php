<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\GoldRates\Infrastructure\Mapper;

use MaciejSz\Nbp\GoldRates\Domain\GoldRate;
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
        $rates = $mapper->rawDataToDomainObject($tables);

        self::assertCount(3, $rates);
        self::assertContainsOnly(GoldRate::class, $rates);
        self::assertEquals(['2023-03-01', '2023-03-02', '2023-03-03'], array_keys($rates));
        self::assertSame(260.89, $rates['2023-03-01']->getRate());
        self::assertSame('2023-03-01T00:00:00+01:00', $rates['2023-03-01']->getDate()->format('c'));
    }

    public function testFailingValidator(): void
    {
        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Invalid value: 260.89');

        $mockValidator = new class implements ThrowableValidator {
            public function validate($value): void
            {
                throw new ValidationException("Invalid value: {$value}");
            }
        };
        $mapper = new GoldRatesMapper($mockValidator);
        $tables = $this->fetchFixtureTables();
        $rates = $mapper->rawDataToDomainObject($tables);
        
        self::markTestIncomplete();
    }

    /**
     * @return array<mixed>
     */
    private function fetchFixtureTables(): array
    {
        $fixturesRepository = new FixturesRepository();

        return $fixturesRepository->fetchArray('/api/cenyzlota/2023-03-01/2023-03-03/data');
    }
}
