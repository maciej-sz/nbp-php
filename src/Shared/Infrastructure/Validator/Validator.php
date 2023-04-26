<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Validator;

/**
 * @template T
 */
interface Validator
{
    /**
     * @param T $value
     */
    public function isValid($value): bool;

    /**
     * @param T $value
     * @throws \Throwable
     */
    public function validate($value): void;
}
