<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Serializer\Exception;

class UnexpectedDataType extends \RuntimeException
{
    public function __construct(
        string $expectedType,
        string $actualType,
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct("Expected {$expectedType}, got {$actualType}", $code, $previous);
    }
}
