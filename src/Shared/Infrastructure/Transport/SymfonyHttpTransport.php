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

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = HttpClient::createForBaseUri(NbpWebClient::BASE_URL);
        }
        $this->httpClient = $httpClient;
    }

    public function request(string $path): array
    {
        $response = $this->httpClient->request('GET', $path, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}
