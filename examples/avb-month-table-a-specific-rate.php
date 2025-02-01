<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::create();

$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    $chfRate = $table->getRate('CHF');
    printf(
        '%s rate from table %s is %F' . PHP_EOL,
        $chfRate->getCurrencyCode(),
        $table->getNo(),
        $chfRate->getValue()
    );
}
