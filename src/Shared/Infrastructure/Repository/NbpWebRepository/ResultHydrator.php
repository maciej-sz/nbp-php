<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;

use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;

/**
 * @template T
 */
class ResultHydrator
{
    /** @var callable(string, string): array<mixed> */
    private $clientCallback;
    /** @var TableMapper<T> */
    private $tableMapper;

    public function __construct(
        callable $clientCallback,
        TableMapper $tableMapper
    )
    {
        $this->clientCallback = $clientCallback;
        $this->tableMapper = $tableMapper;
    }

    public function hydrate(array $results): \Generator
    {

    }
}
