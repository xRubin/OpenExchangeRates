<?php declare(strict_types=1);

namespace OpenExchangeRates;

readonly class UsageResponse
{
    public function __construct(
        public int       $status,
        public UsageData $data
    )
    {
    }
}