<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Validator;

use MaciejSz\Nbp\Shared\Domain\Validator\Exception\ValidationException;

/**
 * @implements Validator<string>
 */
class NbpTableLetterValidator implements Validator
{
    public function isValid($value): bool
    {
        assert(is_string($value));

        return in_array($value, ['A', 'B', 'C'], true);
    }

    public function validate($value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException("Invalid table letter: '{$value}'");
        }
    }
}
