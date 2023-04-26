<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;

final class NbpWebClient implements NbpClient
{
    public const BASE_URL = 'https://api.nbp.pl/';

    /** @var Transport */
    private $transport;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function getCurrencyAveragesTableA(string $startDate, string $endDate): array
    {
        return $this->transport->request(
            "/api/exchangerates/tables/A/{$startDate}/{$endDate}"
        );
    }

    public function getCurrencyAveragesTableB(string $startDate, string $endDate): array
    {
        return $this->transport->request(
            "/api/exchangerates/tables/B/{$startDate}/{$endDate}"
        );
    }

    public function getCurrencyTradingTables(string $startDate, string $endDate): array
    {
        return $this->transport->request(
            "/api/exchangerates/tables/C/{$startDate}/{$endDate}"
        );
    }

    public function getGoldRates(string $startDate, string $endDate): array
    {
        return $this->transport->request(
            "/api/cenyzlota/{$startDate}/{$endDate}"
        );
    }
}
