<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Domain\Exception\NoDataException;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\HttpTransportFactory;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;
use Symfony\Component\HttpClient\Exception\ClientException;

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
        ?TransportFactory $transportFactory = null,
    ): self {
        $baseUri = $baseUri ?: self::BASE_URL;
        $transportFactory = $transportFactory ?: new HttpTransportFactory();
        $transport = $transportFactory->make($baseUri);

        return new self($transport);
    }

    public function send(NbpClientRequest $request): array
    {
        try {
            return $this->transport->get($request->getPath());
        } catch (ClientException|TransportException $e) {
            if ($e->getCode() === 404) {
                throw new NoDataException("Data not found for {$request->getPath()}", 0, $e);
            }
            throw $e;
        }
    }
}
