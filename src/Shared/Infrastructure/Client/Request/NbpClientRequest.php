<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

/**
 * @internal
 */
interface NbpClientRequest
{
    public function getPath(): string;
}
