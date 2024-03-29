<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\HttpTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;

final class NbpWebClient implements NbpClient
{
    public const BASE_URL = 'https://api.nbp.pl/';

    /** @var Transport */
    private $transport;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public static function new(
        ?string $baseUri = null,
        ?TransportFactory $transportFactory = null
    ): self {
        $baseUri = $baseUri ?: self::BASE_URL;
        $transportFactory = $transportFactory ?: new HttpTransportFactory();
        $transport = $transportFactory->create($baseUri);

        return new self($transport);
    }

    public function send(NbpClientRequest $request): array
    {
        return $this->transport->get($request->getPath());
    }
}
