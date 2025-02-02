<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Mapper;

/**
 * @template T
 */
interface TableMapper
{
    /**
     * @param array<array-key, mixed> $tableData
     * @return T
     */
    public function rawDataToDomainObject(array $tableData);
}
