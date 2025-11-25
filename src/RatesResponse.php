<?php declare(strict_types=1);

namespace OpenExchangeRates;

class RatesResponse
{
    /**
     * @param string $disclaimer
     * @param string $license
     * @param int $timestamp
     * @param string $base
     * @param array<string, float> $rates
     */
    public function __construct(
        public readonly string $disclaimer,
        public readonly string $license,
        public readonly int    $timestamp,
        public readonly string $base,
        public readonly array  $rates
    )
    {
    }
}