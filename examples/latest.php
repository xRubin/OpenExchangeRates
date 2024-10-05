<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenExchangeRates\OpenExchangeRatesApi;

$api = new OpenExchangeRatesApi('{APP_ID}');
$api->setSymbols(['USD', 'EUR', 'RUB']);
echo "Symbol\tRate\n";
foreach ($api->getLatest()->rates as $symbol => $rate) {
    printf("%s\t%s\n", $symbol, $rate);
}