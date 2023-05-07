<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;
use MaciejSz\Nbp\Shared\Infrastructure\Validator\NbpTableLetterValidator;
use PHPUnit\Framework\TestCase;

class NbpTableLetterValidatorTest extends TestCase
{
    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(string $value, bool $expectedResult): void
    {
        $validator = new NbpTableLetterValidator();
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

        $validator = new NbpTableLetterValidator();
        $validator->validate($value);
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function isValidDataProvider(): iterable
    {
        return [
            ['A', true],
            ['B', true],
            ['C', true],
            ['D', false],
            ['Z', false],
            ['AA', false],
        ];
    }
}
