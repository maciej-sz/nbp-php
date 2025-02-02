<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

interface Transport
{
    /**
     * @return array<array<array-key, mixed>>
     */
    public function get(string $path): array;
}
