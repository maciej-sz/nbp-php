<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpTableNumberValidator;
use PHPUnit\Framework\TestCase;

class NbpTableNumberValidatorTest extends TestCase
{
    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(string $value, bool $expectedResult): void
    {
        $validator = new NbpTableNumberValidator();
        self::assertSame($expectedResult, $validator->isValid($value));
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testValidate(string $value, bool $isValid): void
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $validator = new NbpTableNumberValidator();
        $validator->validate($value);
    }

    /**
     * @return array<array<int, mixed>>
     */
    public function isValidDataProvider(): array
    {
        return [
            ['042/A/NBP/2012', true],
            ['142/A/NBP/2023', true],
            ['042/B/NBP/2012', true],
            ['142/B/NBP/2023', true],
            ['042/C/NBP/2012', true],
            ['142/C/NBP/2023', true],
            ['042/D/NBP/2012', false],
            ['242/A/NBP/2012', false],
            ['142/A/NBP/1999', false],
        ];
    }
}
