<?php declare(strict_types=1);

namespace OpenExchangeRates;

readonly class RatesResponse
{
    /**
     * @param string $disclaimer
     * @param string $license
     * @param int $timestamp
     * @param string $base
     * @param array<string, float> $rates
     */
    public function __construct(
        public string $disclaimer,
        public string $license,
        public int    $timestamp,
        public string $base,
        public array  $rates
    )
    {
    }
}