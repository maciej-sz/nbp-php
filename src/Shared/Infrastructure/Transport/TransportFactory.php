<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

interface TransportFactory
{
    public function make(string $baseUri): Transport;
}
