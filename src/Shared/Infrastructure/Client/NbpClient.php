<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;

interface NbpClient
{
    /**
     * @return array<mixed>
     */
    public function send(NbpClientRequest $request): array;
}
