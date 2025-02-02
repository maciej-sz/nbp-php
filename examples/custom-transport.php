<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\GoldRatesService;
use MaciejSz\Nbp\Shared\Infrastructure\Client\NbpWebClient;
use MaciejSz\Nbp\Shared\Infrastructure\Repository\NbpWebRepository;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\Transport;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\TransportFactory;

$customTransportFactory = new class implements TransportFactory {
    public function make(string $baseUri): Transport
    {
        return new class implements Transport {
            public function get(string $path): array
            {
                echo "Requesting resource: {$path}" . PHP_EOL;

                $ch = curl_init();
                $url = NbpWebClient::BASE_URL . $path;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                try {
                    $output = curl_exec($ch);
                } finally {
                    curl_close($ch);
                }

                echo 'Request successful' . PHP_EOL;

                return json_decode($output, true);
            }
        };
    }
};

$client = NbpWebClient::new(transportFactory: $customTransportFactory);
$nbpRepository = NbpWebRepository::new($client);
$goldRates = GoldRatesService::new($nbpRepository);

$rate = $goldRates->fromDay('2022-01-03');

printf(
    'Gold rate from %s is %F',
    $rate->getDate()->format('Y-m-d'),
    $rate->getValue()
);
