<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyAverageRatesService;

$currencyAverages = CurrencyAverageRatesService::create();
$rate = $currencyAverages->fromDay('2023-01-02')->fromTable('A')->getRate('USD');

printf('%s rate is %F', $rate->getCurrencyCode(), $rate->getValue());
