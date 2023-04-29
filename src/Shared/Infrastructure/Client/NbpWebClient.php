<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\DefaultTransportFactory;
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

    public static function create(
        ?string $baseUri = null,
        ?TransportFactory $transportFactory = null
    ): self
    {
        if (null === $transportFactory) {
            $transportFactory = new DefaultTransportFactory();
        }
        $transport = $transportFactory->create($baseUri);

        return new self($transport);
    }

    public function send(NbpClientRequest $request): array
    {
        return $this->transport->fetch($request->getPath());
    }
}
