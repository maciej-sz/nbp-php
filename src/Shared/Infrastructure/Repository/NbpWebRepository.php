<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Repository;

use MaciejSz\Nbp\CurrencyAverageRates\Infrastructure\Mapper\CurrencyAveragesTableMapper;
use MaciejSz\Nbp\CurrencyTradingRates\Infrastructure\Mapper\CurrencyTradingTableMapper;
use MaciejSz\Nbp\GoldRates\Infrastructure\Mapper\GoldRatesMapper;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableARequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyAveragesTableBRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\CurrencyTradingTableRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\GoldRatesRequest;
use MaciejSz\Nbp\Shared\Infrastructure\Mapper\TableMapper;

use function MaciejSz\Nbp\Shared\Domain\DateFormatter\month_range;

final class NbpWebRepository implements NbpRepository
{
    /** @var NbpClient */
    private $client;
    /** @var CurrencyAveragesTableMapper */
    private $currencyAveragesTableMapper;
    /** @var CurrencyTradingTableMapper */
    private $currencyTradingTableMapper;
    /** @var GoldRatesMapper */
    private $goldRatesMapper;

    public function __construct(
        NbpClient $client,
        ?CurrencyAveragesTableMapper $currencyAveragesTableMapper = null,
        ?CurrencyTradingTableMapper $currencyTradingTableMapper = null,
        ?GoldRatesMapper $goldRatesMapper = null
    ) {
        if (null === $currencyAveragesTableMapper) {
            $currencyAveragesTableMapper = new CurrencyAveragesTableMapper();
        }
        if (null === $currencyTradingTableMapper) {
            $currencyTradingTableMapper = new CurrencyTradingTableMapper();
        }
        if (null === $goldRatesMapper) {
            $goldRatesMapper = new GoldRatesMapper();
        }
        $this->client = $client;
        $this->currencyAveragesTableMapper = $currencyAveragesTableMapper;
        $this->currencyTradingTableMapper = $currencyTradingTableMapper;
        $this->goldRatesMapper = $goldRatesMapper;
    }

    public static function create(?NbpClient $client = null): self
    {
        if (null === $client) {
            $client = NbpWebClient::create();
        }

        return new self($client);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyAveragesTableA(int $year, int $month): iterable
    {
        $request = new CurrencyAveragesTableARequest(...month_range($year, $month));
        $results = $this->client->send($request);

        yield from $this->hydrate($results, $this->currencyAveragesTableMapper);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyAveragesTableB(int $year, int $month): iterable
    {
        $request = new CurrencyAveragesTableBRequest(...month_range($year, $month));
        $results = $this->client->send($request);

        yield from $this->hydrate($results, $this->currencyAveragesTableMapper);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyTradingTables(int $year, int $month): iterable
    {
        $request = new CurrencyTradingTableRequest(...month_range($year, $month));
        $results = $this->client->send($request);

        yield from $this->hydrate($results, $this->currencyTradingTableMapper);
    }

    /**
     * {@inheritDoc}
     */
    public function getGoldRates(int $year, int $month): iterable
    {
        $request = new GoldRatesRequest(...month_range($year, $month));
        $results = $this->client->send($request);

        yield from $this->hydrate($results, $this->goldRatesMapper);
    }

    /**
     * @template T
     * @param iterable<array<mixed>> $results
     * @param TableMapper<T> $mapper
     * @return iterable<T>
     */
    private function hydrate(iterable $results, TableMapper $mapper): iterable
    {
        foreach ($results as $result) {
            yield $mapper->rawDataToDomainObject($result);
        }
    }
}
