<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Validator;

/**
 * @template T
 */
interface BoolValidator
{
    /**
     * @param T $value
     */
    public function isValid($value): bool;
}
