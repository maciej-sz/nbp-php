<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use GuzzleHttp\Client as GuzzleHttpClient;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;

class HttpTransportFactory implements TransportFactory
{
    public function make(string $baseUri): Transport
    {
        return $this->tryCreateSymfonyTransport($baseUri)
            ?? $this->tryCreateGuzzleTransport($baseUri)
            ?? $this->createFallbackTransport($baseUri);
    }

    public function tryCreateSymfonyTransport(string $baseUri): ?Transport
    {
        if (PHP_VERSION_ID < 80100 || !class_exists(SymfonyHttpClient::class)) {
            return null;
        }

        $symfonyHttpClient = SymfonyHttpClient::createForBaseUri($baseUri);

        return new SymfonyHttpTransport($symfonyHttpClient);
    }

    public function tryCreateGuzzleTransport(string $baseUri): ?Transport
    {
        if (PHP_VERSION_ID < 70205 || !class_exists(GuzzleHttpClient::class)) {
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
