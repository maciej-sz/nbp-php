<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;
use MaciejSz\Nbp\Shared\Domain\Exception\CurrencyCodeNotFoundException;

$currencyAverages = CurrencyAverageRatesService::create();

$bTablesFromMarch = $currencyAverages->getMonthTablesB(2022, 3);

foreach ($bTablesFromMarch as $table) {
    try {
        $rate = $table->getRate('MNT');
    } catch (CurrencyCodeNotFoundException $e) {
        continue;
    }
    printf(
        '%s rate from table %s is %F' . PHP_EOL,
        $rate->getCurrencyName(),
        $table->getNo(),
        $rate->getValue()
    );
}
