<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Domain;

use MaciejSz\Nbp\Shared\Domain\DateTimeBuilder;
use MaciejSz\Nbp\Shared\Domain\Validator\Validator;
use PHPUnit\Framework\TestCase;

class DateBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $validator = $this->createMock(Validator::class);
        $validator->method('validate');

        $builder = new DateTimeBuilder(DateTimeBuilder::DEFAULT_TIMEZONE, $validator);

        $date = $builder->build('2023-01-01');
        $this->assertEquals('2023-01-01T00:00:00+01:00', $date->format('c'));
    }

    public function testBuildInvalid(): void
    {
        $validator = $this->createMock(Validator::class);
        $exception = new \Exception('Invalid date');
        $validator->method('validate')->willThrowException($exception);

        $this->expectExceptionObject($exception);

        $builder = new DateTimeBuilder(DateTimeBuilder::DEFAULT_TIMEZONE, $validator);
        $builder->build('2023-01-01');
    }
}
