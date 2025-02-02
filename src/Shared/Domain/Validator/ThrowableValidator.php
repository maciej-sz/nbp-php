<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Validator;

/**
 * @template T
 */
interface ThrowableValidator
{
    /**
     * @param T $value
     * @throws \Throwable
     */
    public function validate($value): void;
}
