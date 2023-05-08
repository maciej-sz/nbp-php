<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyTradingRatesService;

$currencyTrading = CurrencyTradingRatesService::new();

$tradingRatesFromApril = $currencyTrading->fromMonth(2023, 4);

foreach ($tradingRatesFromApril as $rate) {
    printf(
        "%s rate from %s effective day traded on %s ask price is %s, bid price is %s\n",
        $rate->getCurrencyCode(),
        $rate->getEffectiveDate()->format('Y-m-d'),
        $rate->getTradingDate()->format('Y-m-d'),
        $rate->getAsk(),
        $rate->getBid()
    );
}
