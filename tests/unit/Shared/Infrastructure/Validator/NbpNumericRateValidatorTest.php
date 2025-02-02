<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Domain\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Domain\Validator\NbpNumericRateValidator;
use PHPUnit\Framework\TestCase;

class NbpNumericRateValidatorTest extends TestCase
{
    /**
     * @param mixed $value
     * @dataProvider dataProvider
     */
    public function testIsValid($value, bool $isValid): void
    {
        $validator = new NbpNumericRateValidator();
        self::assertSame($isValid, $validator->isValid($value));
    }

    /**
     * @param mixed $value
     * @dataProvider dataProvider
     */
    public function testValidate($value, bool $isValid): void
    {
        if (!$isValid) {
            self::expectException(ValidationException::class);
        } else {
            self::expectNotToPerformAssertions();
        }

        $validator = new NbpNumericRateValidator();
        $validator->validate($value);
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function dataProvider(): iterable
    {
        return [
            [123, true],
            [123.4, true],
            ['123', true],
            ['123.4', true],
            ['bogus', false],
        ];
    }
}
