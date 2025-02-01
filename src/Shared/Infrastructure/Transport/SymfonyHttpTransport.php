<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SymfonyHttpTransport implements Transport
{
    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public static function create(?HttpClientInterface $httpClient = null): self
    {
        if (null === $httpClient) {
            $httpClient = HttpClient::createForBaseUri(NbpWebClient::BASE_URL);
        }

        return new self($httpClient);
    }

    public function get(string $path): array
    {
        $response = $this->httpClient->request('GET', $path, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}
