<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use GuzzleHttp\Client;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;

class GuzzleTransport implements Transport
{
    /** @var Client */
    private $guzzleClient;

    public function __construct(?Client $guzzleClient = null)
    {
        if (null === $guzzleClient) {
            $guzzleClient = new Client([
                'base_uri' => NbpWebClient::BASE_URL,
            ]);
        }
        $this->guzzleClient = $guzzleClient;
    }

    public function fetch(string $path): array
    {
        $response = $this->guzzleClient->get($path, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        if (null === $data) {
            $baseUri = rtrim($this->guzzleClient->getConfig('base_uri'), '/');
            throw new TransportException("Cannot decode JSON data from {$baseUri}/{$path}");
        }

        return $data;
    }
}