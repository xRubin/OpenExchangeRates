<?php declare(strict_types=1);

namespace OpenExchangeRates;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class OpenExchangeRatesApi
{
    /** @var string Base currency */
    private string $base = 'USD';
    /** @var string[] Request only specific currencies */
    private array $symbols = [];
    /** @var bool Show unofficial, black market and alternative digital currencies */
    private bool $show_alternative = false;

    private ?ClientInterface $client = null;
    private ?TreeMapper $mapper = null;

    public function __construct(
        private readonly string $app_id
    )
    {
    }

    public function setBase(string $base): self
    {
        $this->base = $base;
        return $this;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function setSymbols(array $symbols): self
    {
        $this->symbols = $symbols;
        return $this;
    }

    public function getSymbols(): array
    {
        return $this->symbols;
    }

    public function setShowAlternative(bool $show_alternative): self
    {
        $this->show_alternative = $show_alternative;
        return $this;
    }

    public function isShowAlternative(): bool
    {
        return $this->show_alternative;
    }

    public function setClient(?ClientInterface $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if (null === $this->client) {
            $this->client = new Client(['base_uri' => 'https://openexchangerates.org']);
        }
        return $this->client;
    }

    public function setMapper(?TreeMapper $mapper): OpenExchangeRatesApi
    {
        $this->mapper = $mapper;
        return $this;
    }

    public function getMapper(): ?TreeMapper
    {
        if (null === $this->mapper) {
            $this->mapper = (new MapperBuilder())
                ->allowSuperfluousKeys()
                ->mapper();
        }
        return $this->mapper;
    }

    /**
     * Get the latest exchange rates available from the Open Exchange Rates API.
     * @return RatesResponse
     */
    public function getLatest(): RatesResponse
    {
        $response = $this->getClient()->request('GET', '/api/latest.json', [
            'query' => [
                'app_id' => $this->app_id,
                'base' => $this->base,
                'symbols' => implode(',', $this->symbols),
                'show_alternative' => (string)$this->show_alternative,
                'prettyprint' => 'false',
            ],
        ]);

        return $this->getMapper()->map(RatesResponse::class, Source::json((string)$response->getBody()));
    }

    /**
     * Get historical exchange rates for any date available from the Open Exchange Rates API,
     * currently going back to 1st January 1999.
     * The historical rates returned are the last values we published for a given UTC day
     * (up to and including 23:59:59 UTC), except for the current UTC date.
     * @return RatesResponse
     */
    public function getHistorical(\DateTimeInterface $date): RatesResponse
    {
        $response = $this->getClient()->request(
            'GET',
            sprintf('/api/historical/%s.json', $date->format('Y-m-d')),
            [
                'query' => [
                    'app_id' => $this->app_id,
                    'base' => $this->base,
                    'symbols' => implode(',', $this->symbols),
                    'show_alternative' => (string)$this->show_alternative,
                    'prettyprint' => 'false',
                ],
            ]);

        return $this->getMapper()->map(RatesResponse::class, Source::json((string)$response->getBody()));
    }

    /**
     * List of all currency symbols available from the Open Exchange Rates API, along with their full names,
     * for use in your integration.
     * @return array<string, string>
     */
    public function getCurrencies(): array
    {
        $response = $this->getClient()->request('GET', '/api/currencies.json', [
            'query' => [
                'app_id' => $this->app_id,
                'show_alternative' => (string)$this->show_alternative,
                'prettyprint' => 'false',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Get basic plan information and usage statistics for an Open Exchange Rates App ID.
     * @return UsageResponse
     */
    public function getUsage(): UsageResponse
    {
        $response = $this->getClient()->request('GET', '/api/usage.json', [
            'query' => [
                'app_id' => $this->app_id,
                'prettyprint' => 'false',
            ],
        ]);

        return $this->getMapper()->map(UsageResponse::class, Source::json((string)$response->getBody()));
    }
}