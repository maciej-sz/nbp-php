<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;

/**
 * @internal
 */
class CurrencyTradingTableRequest extends AbstractRangeClientRequest
{
    public function getPath(): string
    {
        return "/api/exchangerates/tables/C/{$this->startDate}/{$this->endDate}";
    }
}
