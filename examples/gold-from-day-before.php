<?php

declare(strict_types=1);

use MaciejSz\Nbp\Service\GoldRatesService;

require_once __DIR__ . '/../vendor/autoload.php';

$goldRates = GoldRatesService::create();

$goldRateBeforeJan2nd = $goldRates->fromDayBefore('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateBeforeJan2nd->getDate()->format('Y-m-d'),
    $goldRateBeforeJan2nd->getValue()
);
