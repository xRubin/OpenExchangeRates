# OpenSky REST API

[![Build Status](https://github.com/xRubin/OpenExchangeRates/workflows/CI/badge.svg)](https://github.com/xRubin/OpenExchangeRates/actions)
[![Latest Stable Version](http://poser.pugx.org/rubin/openexchangerates/v)](https://packagist.org/packages/rubin/openexchangerates)
[![Coverage Status](https://coveralls.io/repos/github/xRubin/OpenExchangeRates/badge.svg?branch=master)](https://coveralls.io/github/xRubin/OpenExchangeRates?branch=master)
[![PHP Version Require](http://poser.pugx.org/rubin/openexchangerates/require/php)](https://packagist.org/packages/rubin/openexchangerates)

PHP implementation for the [OpenExchangeRates.org](https://openexchangerates.org/) REST API.
This library is based on the [REST API docs](https://docs.openexchangerates.org/reference/api-introduction).

## Installation
With composer:
```bash
composer require rubin/openexchangerates
```

## Usage
Create API connector:
```php
$api = new OpenExchangeRates\OpenExchangeRatesApi('{APP_ID}');
```
## Extra parameters
### Set Base Currency ('base')
The default base currency of the API is US Dollars (USD), but you can request exchange rates relative to a different base currency, where available, by setting the base parameter in your request.
```php
$api->setBase('EUR');
````
### Get Specific Currencies ('symbols')
By default, the API returns rates for all currencies, but if you need to minimise transfer size, you can request a limited subset of exchange rates, where available, by setting the symbols parameter in your request.
```php
$api->setSymbols(['AMD', 'EUR']);
````
### Alternative Rates ('show_alternative')
You may now request latest and historical rates for unofficial, black market and alternative digital currencies by adding a simple API parameter onto your request.
```php
$api->setShowAlternative(true);
````
## API endpoints
### /latest
Get the latest exchange rates available from the Open Exchange Rates API.

The most simple route in our API, latest.json provides a standard response object containing all the conversion rates for all of the currently available symbols/currencies, labeled by their international-standard 3-letter ISO currency codes.

The latest rates will always be the most up-to-date data available on your plan.
```php
echo "Symbol\tRate\n";
foreach ($api->getLatest()->rates as $symbol => $rate) {
    printf("%s\t%s\n", $symbol, $rate);
}
```

### /historical
Get historical exchange rates for any date available from the Open Exchange Rates API, currently going back to 1st January 1999.

The historical rates returned are the last values published for a given UTC day (up to and including 23:59:59 UTC), except for the current UTC date.
```php
echo "Symbol\tRate\n";
foreach ($api->getHistorical(new \DateTimeImmutable('2012-07-10'))->rates as $symbol => $rate) {
    printf("%s\t%s\n", $symbol, $rate);
}
```

### /currencies
Get a JSON list of all currency symbols available from the Open Exchange Rates API, along with their full names, for use in your integration.

This list will always mirror the currencies available in the latest rates (given as their 3-letter codes).
```php
echo "Symbol\tName\n";
foreach ($api->getCurrencies() as $symbol => $name) {
    printf("%s\t%s\n", $symbol, $name);
}
```

### /usage
Get basic plan information and usage statistics for an Open Exchange Rates App ID
```php
var_dump($api->getUsage());
```
