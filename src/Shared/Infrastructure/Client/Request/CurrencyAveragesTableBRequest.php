<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

/**
 * @internal
 */
class CurrencyAveragesTableBRequest extends AbstractRangeClientRequest
{
    public function getPath(): string
    {
        return "/api/exchangerates/tables/B/{$this->startDate}/{$this->endDate}";
    }
}
