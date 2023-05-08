<?php

declare(strict_types=1);

use MaciejSz\Nbp\Service\GoldRatesService;

require_once __DIR__ . '/../vendor/autoload.php';

$goldRates = GoldRatesService::new();

$goldRateFromJan2nd2014 = $goldRates->fromDay('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateFromJan2nd2014->getDate()->format('Y-m-d'),
    $goldRateFromJan2nd2014->getValue()
);
