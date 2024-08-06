<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::new();

$eurRateFromApril4th = $currencyAverages
    ->fromDay('2024-08-05')
    ->fromTable('A')
    ->getRate('EUR')
;

echo $eurRateFromApril4th->getValue() . PHP_EOL;
