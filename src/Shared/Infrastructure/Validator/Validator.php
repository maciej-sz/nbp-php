<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Validator;

/**
 * @template T
 * @implements BoolValidator<T>
 * @implements ThrowableValidator<T>
 */
interface Validator extends BoolValidator, ThrowableValidator
{
}
