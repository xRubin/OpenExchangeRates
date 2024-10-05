<?php declare(strict_types=1);

namespace tests;

use CuyZ\Valinor\MapperBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OpenExchangeRates\OpenExchangeRatesApi;
use OpenExchangeRates\PlanInformation;
use OpenExchangeRates\RatesResponse;
use OpenExchangeRates\UsageData;
use OpenExchangeRates\UsageInformation;
use OpenExchangeRates\UsageResponse;
use PHPUnit\Framework\TestCase;

final class OpenExchangeRatesApiTest extends TestCase
{
    public function testCanChangeBase()
    {
        $api = new OpenExchangeRatesApi('{APP_ID}');
        $this->assertNotEquals('EUR', $api->getBase());
        $api->setBase('EUR');
        $this->assertEquals('EUR', $api->getBase());
    }

    public function testCanChangeSymbols()
    {
        $api = new OpenExchangeRatesApi('{APP_ID}');
        $this->assertNotEquals(['AMD', 'EUR'], $api->getSymbols());
        $api->setSymbols(['AMD', 'EUR']);
        $this->assertEquals(['AMD', 'EUR'], $api->getSymbols());
    }

    public function testCanChangeShowAlternative()
    {
        $api = new OpenExchangeRatesApi('{APP_ID}');
        $this->assertFalse($api->isShowAlternative());
        $api->setShowAlternative(true);
        $this->assertTrue($api->isShowAlternative());
    }

    public function testCanChangeMapper()
    {
        $api = new OpenExchangeRatesApi('{APP_ID}');
        $origin = $api->getMapper();
        $api->setMapper((new MapperBuilder())->enableFlexibleCasting()->mapper());
        $this->assertNotEquals($origin, $api->getMapper());
    }

    public function testCanChangeClient()
    {
        $api = new OpenExchangeRatesApi('{APP_ID}');
        $origin = $api->getClient();
        $api->setClient(new Client(['base_uri' => 'http://localhost/']));
        $this->assertNotEquals($origin, $api->getClient());
    }

    public function testCanParseLatest()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'disclaimer' => 'https://openexchangerates.org/terms/',
                'license' => 'https://openexchangerates.org/license/',
                'timestamp' => 1449877801,
                'base' => 'USD',
                'rates' => [
                    'AED' => 3.672538,
                    'AFN' => 66.809999,
                    'ALL' => 125.716501,
                    'AMD' => 484.902502,
                    'ANG' => 1.788575,
                    'AOA' => 135.295998,
                    'ARS' => 9.750101,
                    'AUD' => 1.390866,
                ]
            ])),
        ]);

        $api = new OpenExchangeRatesApi('{APP_ID}');
        $api->setClient(new Client(['handler' => HandlerStack::create($mock)]));
        $this->assertEquals(new RatesResponse(
            disclaimer: 'https://openexchangerates.org/terms/',
            license: 'https://openexchangerates.org/license/',
            timestamp: 1449877801,
            base: 'USD',
            rates: [
                'AED' => 3.672538,
                'AFN' => 66.809999,
                'ALL' => 125.716501,
                'AMD' => 484.902502,
                'ANG' => 1.788575,
                'AOA' => 135.295998,
                'ARS' => 9.750101,
                'AUD' => 1.390866,
            ]
        ), $api->getLatest());
    }

    public function testCanParseHistorical()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'disclaimer' => 'https://openexchangerates.org/terms/',
                'license' => 'https://openexchangerates.org/license/',
                'timestamp' => 1341936000,
                'base' => 'USD',
                'rates' => [
                    'AED' => 3.672914,
                    'AFN' => 48.337601,
                    'ALL' => 111.863334
                ]
            ])),
        ]);

        $api = new OpenExchangeRatesApi('{APP_ID}');
        $api->setClient(new Client(['handler' => HandlerStack::create($mock)]));
        $this->assertEquals(new RatesResponse(
            disclaimer: 'https://openexchangerates.org/terms/',
            license: 'https://openexchangerates.org/license/',
            timestamp: 1341936000,
            base: 'USD',
            rates: [
                'AED' => 3.672914,
                'AFN' => 48.337601,
                'ALL' => 111.863334
            ]
        ), $api->getHistorical(new \DateTimeImmutable('2012-07-10')));
    }

    public function testCanParseCurrencies()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'AED' => 'United Arab Emirates Dirham',
                'AFN' => 'Afghan Afghani',
                'ALL' => 'Albanian Lek',
            ])),
        ]);

        $api = new OpenExchangeRatesApi('{APP_ID}');
        $api->setClient(new Client(['handler' => HandlerStack::create($mock)]));
        $this->assertEquals([
            'AED' => 'United Arab Emirates Dirham',
            'AFN' => 'Afghan Afghani',
            'ALL' => 'Albanian Lek',
        ], $api->getCurrencies());
    }

    public function testCanParseUsage()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 200,
                'data' => (object)[
                    'app_id' => '{APP_ID}',
                    'status' => 'active',
                    'plan' => (object)[
                        'name' => 'Enterprise',
                        'quota' => '100,000 requests/month',
                        'update_frequency' => '30-minute',
                        'features' => (object)[
                            'base' => true,
                            'symbols' => true,
                            'experimental' => true,
                            'time-series' => true,
                            'convert' => false
                        ]
                    ],
                    'usage' => (object)[
                        'requests' => 54524,
                        'requests_quota' => 100000,
                        'requests_remaining' => 45476,
                        'days_elapsed' => 16,
                        'days_remaining' => 14,
                        'daily_average' => 2842
                    ]
                ]
            ])),
        ]);

        $api = new OpenExchangeRatesApi('{APP_ID}');
        $api->setClient(new Client(['handler' => HandlerStack::create($mock)]));
        $this->assertEquals(new UsageResponse(
            status: 200,
            data: new UsageData(
                app_id: '{APP_ID}',
                status: 'active',
                plan: new PlanInformation(
                    name: 'Enterprise',
                    quota: '100,000 requests/month',
                    update_frequency: '30-minute',
                    features: [
                        'base' => true,
                        'symbols' => true,
                        'experimental' => true,
                        'time-series' => true,
                        'convert' => false
                    ]
                ),
                usage: new UsageInformation(
                    requests: 54524,
                    requests_quota: 100000,
                    requests_remaining: 45476,
                    days_elapsed: 16,
                    days_remaining: 14,
                    daily_average: 2842
                )
            )
        ), $api->getUsage());
    }
}
