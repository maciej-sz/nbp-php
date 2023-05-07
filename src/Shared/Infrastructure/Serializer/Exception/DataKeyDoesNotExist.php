<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Serializer\Exception;

class DataKeyDoesNotExist extends \RuntimeException
{
    public function __construct(string $key = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Data key '{$key}' does not exist", $code, $previous);
    }
}
