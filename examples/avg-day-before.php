<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::new();
$eurRateFromBeforeJanuary2nd = $currencyAverages
    ->fromDayBefore('2024-07-28')
    ->fromTable('A')
    ->getRate('EUR')
;

printf(
    '%s rate from %s is %F' . PHP_EOL,
    $eurRateFromBeforeJanuary2nd->getCurrencyCode(),
    $eurRateFromBeforeJanuary2nd->getEffectiveDate()->format('Y-m-d'),
    $eurRateFromBeforeJanuary2nd->getValue()
);
