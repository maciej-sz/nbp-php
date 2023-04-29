<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use GuzzleHttp\Client as GuzzleHttpClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;

class DefaultTransportFactory implements TransportFactory
{
    public function create(string $baseUri = NbpWebClient::BASE_URL): Transport
    {
        return $this->tryCreateSymfonyTransport($baseUri)
            ?? $this->tryCreateGuzzleTransport($baseUri)
            ?? $this->createFallbackTransport($baseUri)
        ;
    }

    private function tryCreateSymfonyTransport(string $baseUri): ?Transport
    {
        if (!class_exists(SymfonyHttpClient::class)) {
            return null;
        }

        $symfonyHttpClient = SymfonyHttpClient::createForBaseUri($baseUri);

        return new SymfonyHttpTransport($symfonyHttpClient);
    }

    private function tryCreateGuzzleTransport(string $baseUri): ?Transport
    {
        if (!class_exists(GuzzleHttpClient::class)) {
            return null;
        }

        $guzzleHttpClient = new GuzzleHttpClient([
            'base_uri' => $baseUri,
        ]);

        return new GuzzleTransport($guzzleHttpClient);
    }

    public function createFallbackTransport(string $baseUri): Transport
    {
        return new FileContentsTransport($baseUri);
    }
}
