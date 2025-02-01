<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::create();

$bTablesFromMarch = $currencyAverages->getMonthTablesB(2024, 8);

foreach ($bTablesFromMarch as $table) {
    foreach ($table->getRates() as $rate) {
        printf(
            '%s rate from table %s is %F' . PHP_EOL,
            $rate->getCurrencyCode(),
            $table->getNo(),
            $rate->getValue()
        );
    }
}
