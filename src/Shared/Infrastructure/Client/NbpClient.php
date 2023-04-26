<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client;

interface NbpClient
{
    /**
     * @return array<mixed>
     */
    public function getCurrencyAveragesTableA(string $startDate, string $endDate): array;

    /**
     * @return array<mixed>
     */
    public function getCurrencyAveragesTableB(string $startDate, string $endDate): array;

    /**
     * @return array<mixed>
     */
    public function getCurrencyTradingTables(string $startDate, string $endDate): array;

    /**
     * @return array<mixed>
     */
    public function getGoldRates(string $startDate, string $endDate): array;
}
