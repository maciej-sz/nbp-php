<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Service;

use Doctrine\Common\Cache\Cache;

class NbpCache
{
    /** @var string */
    private $keyPrefix;
    /** @var Cache */
    private $backend;
    /** @var int */
    private $lifeTime;

    public function __construct(
        string $keyPrefix,
        Cache $backend,
        int $lifeTime = 0
    ) {
        $this->keyPrefix = $keyPrefix;
        $this->backend = $backend;
        $this->lifeTime = $lifeTime;
    }

    public function tryGet(int $year, int $month): ?array
    {
        return null;
    }

    public function getKey(int $year, int $month): string
    {
        $dateSuffix = $this->getDateHelper()->yearAndMonthFormat($year, $month);

        return "{$this->keyPrefix}_{$dateSuffix}";
    }
}
