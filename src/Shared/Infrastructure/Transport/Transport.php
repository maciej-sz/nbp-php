<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

interface Transport
{
    public function get(string $path): array;
}
