<?php

declare(strict_types=1);

use MaciejSz\Nbp\Service\GoldRatesService;

require_once __DIR__ . '/../vendor/autoload.php';

$goldRates = GoldRatesService::create();

$jan2013rates = $goldRates->fromMonth(2013, 1);

foreach ($jan2013rates as $rate) {
    printf(
        'Gold rate from %s is %F' . PHP_EOL,
        $rate->getDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
