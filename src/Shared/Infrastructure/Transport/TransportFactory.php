<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;

interface TransportFactory
{
    public function create(string $baseUri = NbpWebClient::BASE_URL): Transport;
}
