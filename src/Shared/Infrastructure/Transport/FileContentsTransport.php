<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;

class FileContentsTransport implements Transport
{
    /** @var string */
    private $baseUri;

    public function __construct(string $baseUri = NbpWebClient::BASE_URL)
    {
        $this->baseUri = $baseUri;
    }

    public function fetch(string $path): array
    {
        $baseUri = trim($this->baseUri, '/');
        $path = trim($path, '/');
        $uri = "{$baseUri}/{$path}?format=json";
        $contents = file_get_contents($uri);
        $data = json_decode($contents, true);
        if (null === $data) {
            throw new TransportException("Cannot decode JSON data from {$uri}");
        }

        return $data;
    }
}
