<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MaciejSz\Nbp\Service\CurrencyTradingRatesService;

$currencyTrading = CurrencyTradingRatesService::new();

$gbpFromApril4th = $currencyTrading->fromEffectiveDay('2023-04-04')->getRate('GBP');

printf(
    '%s rate from %s effective day traded on %s ask price is %s, bid price is %s',
    $gbpFromApril4th->getCurrencyCode(),
    $gbpFromApril4th->getEffectiveDate()->format('Y-m-d'),
    $gbpFromApril4th->getTradingDate()->format('Y-m-d'),
    $gbpFromApril4th->getAsk(),
    $gbpFromApril4th->getBid()
);
