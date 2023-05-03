<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Mapper;

/**
 * @template T
 */
interface TableMapper
{
    /**
     * @param array<mixed> $tableData
     * @return T
     */
    public function rawDataToDomainObject(array $tableData): object;
}
