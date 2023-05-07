<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

/**
 * @internal
 */
class CurrencyAveragesTableARequest extends AbstractRangeClientRequest
{
    public function getPath(): string
    {
        return "/api/exchangerates/tables/A/{$this->startDate}/{$this->endDate}";
    }
}
