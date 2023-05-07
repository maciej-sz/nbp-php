<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Validator;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Exception\ValidationException;

/**
 * @implements Validator<string>
 */
class NbpDateStringValidator implements Validator
{
    private const REGEX = '/^(20[12][0-9])\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/';
    private const MIN_DATE = '2012-01-02';

    /**
     * {@inheritDoc}
     */
    public function isValid($value): bool
    {
        assert(is_string($value));
        if (0 === preg_match(self::REGEX, $value)) {
            return false;
        }

        return ($value <=> self::MIN_DATE) >= 0;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException("Invalid date string: '{$value}'");
        }
    }
}
