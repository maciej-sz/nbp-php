<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Validator;

use MaciejSz\Nbp\Shared\Domain\Validator\Exception\ValidationException;

/**
 * @implements Validator<mixed>
 */
class NbpNumericRateValidator implements Validator
{
    public function isValid($value): bool
    {
        return is_numeric($value);
    }

    public function validate($value): void
    {
        if (!$this->isValid($value)) {
            $stringRepresentation = (is_string($value) || is_numeric($value)) ? $value : 'value';
            throw new ValidationException("Invalid numeric rate value: '{$stringRepresentation}'");
        }
    }
}
