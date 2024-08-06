<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

/**
 * @internal
 */
class CurrencyAveragesLastTableBRequest implements NbpClientRequest
{
    public function getPath(): string
    {
        return "/api/exchangerates/tables/B/last/1";
    }
}
