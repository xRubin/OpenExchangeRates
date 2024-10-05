<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenExchangeRates\OpenExchangeRatesApi;

$api = new OpenExchangeRatesApi('{APP_ID}');
echo "Symbol\tName\n";
foreach ($api->getCurrencies() as $symbol => $name) {
    printf("%s\t%s\n", $symbol, $name);
}