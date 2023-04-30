<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Serializer;

interface DataAccess
{
    public function extract(string $key);

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractString(string $key): string;

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractFloat(string $key): float;

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractDateTime(string $key): \DateTimeInterface;

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractArray(string $key): array;
}
