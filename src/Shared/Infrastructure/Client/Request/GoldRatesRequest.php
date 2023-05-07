<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

class GoldRatesRequest extends AbstractRangeClientRequest
{
    public function getPath(): string
    {
        return "/api/cenyzlota/{$this->startDate}/{$this->endDate}";
    }
}
