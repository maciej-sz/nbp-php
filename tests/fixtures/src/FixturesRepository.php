<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Fixtures;

use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingRate;
use MaciejSz\Nbp\CurrencyTradingRates\Domain\CurrencyTradingTable;

class FixturesRepository
{
    public static function create(): self
    {
        return new self();
    }

    public function fetchJson(string $fixturePath): string
    {
        $contents = file_get_contents($this->getFullFixturePath($fixturePath, 'json'));
        if (false === $contents) {
            throw new \Exception("Cannot read fixture file: {$fixturePath}");
        }

        return $contents;
    }

    /**
     * @return array<mixed>
     */
    public function fetchArray(string $fixturePath): array
    {
        $data = json_decode($this->fetchJson($fixturePath), true);
        assert(is_array($data));

        return $data;
    }

    /**
     * @return array<
     *     array{
     *         table: string,
     *         no: string,
     *         effectiveDate: string,
     *         rates: array<
     *             array{
     *                 currency: string,
     *                 code: string,
     *                 mid: float
     *             }
     *         >
     *     }
     * >
     */
    public function fetchAverageTablesJson(string $table, string $from, string $to): array
    {
        // @phpstan-ignore-next-line
        return $this->fetchArray("/api/exchangerates/tables/{$table}/{$from}/{$to}/data");
    }

    /**
     * @return array<
     *     array{
     *         table: string,
     *         no: string,
     *         tradingDate: string,
     *         effectiveDate: string,
     *         rates: array<
     *             array{
     *                 currency: string,
     *                 code: string,
     *                 bid: float,
     *                 ask: float
     *             }
     *         >
     *     }
     * >
     */
    public function fetchTradingTablesJson(string $from, string $to): array
    {
        // @phpstan-ignore-next-line
        return $this->fetchArray("/api/exchangerates/tables/C/{$from}/{$to}/data");
    }

    /**
     * @return array<CurrencyTradingTable>
     */
    public function fetchTradingTables(string $from, string $to): array
    {
        return require $this->getFullFixturePath("/api/exchangerates/tables/C/{$from}/{$to}/tables", 'php');
    }

    /**
     * @return array<CurrencyTradingRate>
     */
    public function fetchTradingRates(string $from, string $to): array
    {
        return require $this->getFullFixturePath("/api/exchangerates/tables/C/{$from}/{$to}/rates", 'php');
    }

    public function getFullFixturePath(string $basePath, string $fileExt): string
    {
        $expectedFixturesResourceDir = __DIR__ . '/../resources';
        $fixturesResourceDir = realpath($expectedFixturesResourceDir);
        if (false === $fixturesResourceDir) {
            throw new \Exception("Fixtures resource dir is missing. Expected at: {$expectedFixturesResourceDir}");
        }

        $basePath = ltrim($basePath, '/');
        $expectedFixtureFullPath =
            $fixturesResourceDir
            . "/{$basePath}"
            . ((!empty($fileExt)) ? ".{$fileExt}" : '');
        $fixtureFullPath = realpath($expectedFixtureFullPath);
        if (false === $fixtureFullPath) {
            throw new \Exception("Fixture file not found. Searched at: {$expectedFixtureFullPath}");
        }
        if (strpos($fixtureFullPath, $fixturesResourceDir) !== 0) {
            throw new \Exception('Security breach: fixture file path cannot go above fixtures directory');
        }

        return $fixtureFullPath;
    }
}
