<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenExchangeRates\OpenExchangeRatesApi;

$api = new OpenExchangeRatesApi('{APP_ID}');
var_dump($api->getUsage());