<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;

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
            throw new ValidationException("Invalid numeric rate value: '{$value}'");
        }
    }
}
