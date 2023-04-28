<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;

final class NbpWebClient implements NbpClient
{
    public const BASE_URL = 'https://api.nbp.pl/';

    /** @var Transport */
    private $transport;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function send(NbpClientRequest $request): array
    {
        return $this->transport->fetch($request->getPath());
    }
}
