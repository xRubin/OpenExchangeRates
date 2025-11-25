<?php declare(strict_types=1);

namespace OpenExchangeRates;

class UsageResponse
{
    public function __construct(
        public readonly int       $status,
        public readonly UsageData $data
    )
    {
    }
}