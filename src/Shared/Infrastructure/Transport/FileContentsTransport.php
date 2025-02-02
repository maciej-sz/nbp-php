<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;

class FileContentsTransport implements Transport
{
    /** @var string */
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public static function create(string $baseUri = NbpWebClient::BASE_URL): self
    {
        return new self($baseUri);
    }

    public function get(string $path): array
    {
        $baseUri = trim($this->baseUri, '/');
        $path = trim($path, '/');
        $uri = "{$baseUri}/{$path}?format=json";

        $errorMessage = null;
        set_error_handler(static function (
            int $errno,
            string $errstr,
            string $errfile,
            int $errline,
        ) use (&$errorMessage): bool {
            $errorMessage = $errstr;

            return true;
        });

        try {
            $contents = file_get_contents($uri);
        } finally {
            restore_error_handler();
        }

        if (false === $contents) {
            $code = 0;
            if (mb_strpos((string) $errorMessage, '404 Not Found') !== false) {
                $code = 404;
            }
            throw new TransportException("Cannot get contents from {$uri}", $code);
        }
        /** @var ?array<array<array-key, mixed>> $data */
        $data = json_decode($contents, true);
        if (null === $data) {
            throw new TransportException("Cannot decode JSON data from {$uri}");
        }

        return $data;
    }
}
