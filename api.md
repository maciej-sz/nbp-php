# CurrencyAverageRatesService

This service provides an API for accessing the average rates published in NBP tables.

## `fromDay` method

Returns a dictionary of NBP tables for a given day.

<details open>
<summary>Example</summary>

```php
$eurRateFromApril4th = $currencyAverages
    ->fromDay('2023-04-04')
    ->fromTable('A')
    ->getRate('EUR');

echo $eurRateFromApril4th->getValue(); // 4.6785
```

</details>

## `fromDayBefore` method

This method returns rates corresponding to the previous business day to the one provided as the argument.

This can be useful in some bookkeeping applications when there is a legislatory need to calculate
transfer prices. The legislation requires for the prices to be calculated
using a currency rate applied in the business day before the actual transfer date.

<details open>
<summary>Example</summary>

```php
$eurRateFromBeforeJanuary2nd = $currencyAverages
    ->fromDayBefore('2023-01-02')
    ->fromTable('A')
    ->getRate('EUR')
;

printf(
    '%s rate from %s is %F',
    $eurRateFromBeforeJanuary2nd->getCurrencyCode(),
    $eurRateFromBeforeJanuary2nd->getEffectiveDate()->format('Y-m-d'),
    $eurRateFromBeforeJanuary2nd->getValue()
);
```
```
EUR rate from 2022-12-30 is 4.689900
```

</details>

## `fromMonth` method

Returns a flat collection of rates from all NBP tables for a given month

<details>
<summary>Example</summary>

```php
$averageRatesFromJanuary = $currencyAverages->fromMonth(2023, 1);

foreach ($averageRatesFromJanuary as $rate) {
    printf(
        '%s rate from %s is %F' . PHP_EOL,
        $rate->getCurrencyCode(),
        $rate->getEffectiveDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
```

```
THB rate from 2023-01-02 is 0.126700
USD rate from 2023-01-02 is 4.381100
AUD rate from 2023-01-02 is 2.976700
...
```

</details>

## `getMonthTablesA` method

Returns the `A` table iterator from a specific month.

Rates here are grouped into tables, which represent the actual data structure provided by NBP.
To get the actual rates, a second iteration is required:

<details>
<summary>Example: getting all currency rates</summary>

```php
$aTablesFromMarch = $currencyAverages->getMonthTablesA(2023, 3);

foreach ($aTablesFromMarch as $table) {
    foreach ($table->getRates() as $rate) {
        printf(
            '%s rate from table %s is %F' . PHP_EOL,
            $rate->getCurrencyCode(),
            $table->getNo(),
            $rate->getValue()
        );
    }
}
```

```
THB rate from table 042/A/NBP/2023 is 0.126700
USD rate from table 042/A/NBP/2023 is 4.409400
AUD rate from table 042/A/NBP/2023 is 2.981900
...
THB rate from table 043/A/NBP/2023 is 0.126600
USD rate from table 043/A/NBP/2023 is 4.400200
AUD rate from table 043/A/NBP/2023 is 2.963800
...
```

</details>

<details>
<summary>Example: getting specific currency rate</summary>

```php
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
```

```
CHF rate from table 042/A/NBP/2023 is 4.703100
CHF rate from table 043/A/NBP/2023 is 4.674300
CHF rate from table 044/A/NBP/2023 is 4.728000
// ...
```

</details>

## `getMonthTablesB` method

Returns the `B` table iterator from a specific month.

<details>
<summary>Example</summary>

```php
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
```

```
tugrik (Mongolia) rate from table 009/B/NBP/2022 is 0.001500
tugrik (Mongolia) rate from table 010/B/NBP/2022 is 0.001529
tugrik (Mongolia) rate from table 011/B/NBP/2022 is 0.001469
tugrik (Mongolia) rate from table 012/B/NBP/2022 is 0.001457
tugrik (Mongolia) rate from table 013/B/NBP/2022 is 0.001417
```

</details>

### Warning about missing currencies in table "B"

In table `B` there can be multiple currencies with the same code.

It is also possible, that a specific currency is present in table from one day,
but is not present in table from the next day.

In such case, you should not use the `getRate($rate)` method but rather
iterate over all currencies returned by `getRates()`.
<br /><br/>

# Currency trading rates service

This service is used to get buy and sell currency rates from NBP tables.

## `fromMonth` method

Returns trading rates from the entire month.

<details>
<summary>Example</summary>

```php
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
```

```
USD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 4.3338, bid price is 4.248
AUD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 2.9072, bid price is 2.8496
CAD rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 3.2033, bid price is 3.1399
EUR rate from 2023-04-03 effective day traded on 2023-03-31 ask price is 4.7208, bid price is 4.6274
...
```

</details>

## `fromEffectiveDay` method

Returns rates from the effective date.

<details>
<summary>Example</summary>

```php
$gbpFromApril4th = $currencyTrading->fromEffectiveDay('2023-04-04')->getRate('GBP');

printf(
    '%s rate from %s effective day traded on %s ask price is %s, bid price is %s',
    $gbpFromApril4th->getCurrencyCode(),
    $gbpFromApril4th->getEffectiveDate()->format('Y-m-d'),
    $gbpFromApril4th->getTradingDate()->format('Y-m-d'),
    $gbpFromApril4th->getAsk(),
    $gbpFromApril4th->getBid()
);
```

```
GBP rate from 2023-04-04 effective day traded on 2023-04-03 ask price is 5.3691, bid price is 5.2627
```

</details>

## `fromTradingDay` method

Returns rates from the trading date.

<details>
<summary>Example</summary>

```php
$gbpFromApril4th = $currencyTrading->fromTradingDay('2023-04-04')->getRate('GBP');

printf(
    '%s rate from %s effective day traded on %s ask price is %s, bid price is %s',
    $gbpFromApril4th->getCurrencyCode(),
    $gbpFromApril4th->getEffectiveDate()->format('Y-m-d'),
    $gbpFromApril4th->getTradingDate()->format('Y-m-d'),
    $gbpFromApril4th->getAsk(),
    $gbpFromApril4th->getBid()
);
```

```
GBP rate from 2023-04-05 effective day traded on 2023-04-04 ask price is 5.4035, bid price is 5.2965
```

</details>
<br/>

# Gold rates service

This service is used to get gold commodity rates from NBP tables.

## `fromMonth` method

Gets all rates for a specific month.

<details>
<summary>Example</summary>

```php
$jan2013rates = $goldRates->fromMonth(2013, 1);

foreach ($jan2013rates as $rate) {
    printf(
        'Gold rate from %s is %F' . PHP_EOL,
        $rate->getDate()->format('Y-m-d'),
        $rate->getValue()
    );
}
```

```
Gold rate from 2013-01-02 is 165.830000
Gold rate from 2013-01-03 is 166.970000
Gold rate from 2013-01-04 is 167.430000
...
```

</details>

## `fromDay` method

Returns a gold rate for a specific date.

<details>
<summary>Example</summary>

```php
$goldRateFromJan2nd2014 = $goldRates->fromDay('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateFromJan2nd2014->getDate()->format('Y-m-d'),
    $goldRateFromJan2nd2014->getValue()
);
```

```
Gold rate from 2014-01-02 is 116.350000
```

</details>

## `fromDayBefore` method

Returns a gold rate for the business day preceding a specific date.

<details>
<summary>Example</summary>

```php
$goldRateBeforeJan2nd = $goldRates->fromDayBefore('2014-01-02');

printf(
    'Gold rate from %s is %F',
    $goldRateBeforeJan2nd->getDate()->format('Y-m-d'),
    $goldRateBeforeJan2nd->getValue()
);

```

```
Gold rate from 2013-12-31 is 116.890000
```

</details>
