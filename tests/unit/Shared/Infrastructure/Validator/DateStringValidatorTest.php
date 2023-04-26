<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpDateStringValidator;
use PHPUnit\Framework\TestCase;

class DateStringValidatorTest extends TestCase
{
    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(string $date, bool $expectedResult)
    {
        $validator = new NbpDateStringValidator();
        self::assertSame($expectedResult, $validator->isValid($date));
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testValidate(string $value, bool $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $validator = new NbpDateStringValidator();
        $validator->validate($value);
    }

    /**
     * @return array<array<string, bool>>
     */
    public function isValidDataProvider(): array
    {
        return [
            ['1999-01-01', false],
            ['2011-12-31', false],
            ['2012-01-01', false],
            ['2012-01-02', true],
            ['2012-1-02', true],
            ['2012-01-2', true],
            ['2012-1-2', true],
            ['2022-12-31', true],
        ];
    }
}
