<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::new();

$averageRatesFromJanuary = $currencyAverages->fromMonth(2023, 1);
foreach ($averageRatesFromJanuary as $rate) {
    printf(
        '%s rate from %s is %F' . PHP_EOL,
        $rate->getCurrencyCode(),
        $rate->getEffectiveDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
