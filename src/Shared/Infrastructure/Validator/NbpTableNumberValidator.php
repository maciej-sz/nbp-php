<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;

/**
 * @implements Validator<string>
 */
class NbpTableNumberValidator implements Validator
{
    private const REGEX = '/^[01][0-9]{2}\/[ABC]\/NBP\/20[0-9]{2}$/';

    public function isValid($value): bool
    {
        assert(is_string($value));

        return 1 === preg_match(self::REGEX, $value);
    }

    public function validate($value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException("Invalid NBP table number: '{$value}'");
        }
    }
}
