<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;

class GuzzleTransport implements Transport
{
    /** @var Client */
    private $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public static function create(?Client $guzzleClient = null): self
    {
        if (null === $guzzleClient) {
            $guzzleClient = new Client([
                'base_uri' => NbpWebClient::BASE_URL,
            ]);
        }

        return new self($guzzleClient);
    }

    public function get(string $path): array
    {
        $path = trim($path, '/');
        $response = $this->guzzleClient->get($path, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        /** @var ?array<array<mixed>> $data */
        $data = json_decode($response->getBody()->getContents(), true);
        if (null === $data) {
            /** @var Uri $baseUri */
            $baseUri = $this->guzzleClient->getConfig('base_uri');
            throw new TransportException("Cannot decode JSON data from {$baseUri}/{$path}");
        }

        return $data;
    }
}
