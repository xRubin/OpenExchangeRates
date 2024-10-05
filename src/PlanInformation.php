<?php declare(strict_types=1);

namespace OpenExchangeRates;

readonly class PlanInformation
{
    /**
     * @param string $name The name of the current plan
     * @param string $quota The monthly request allowance (formatted string for display)
     * @param string $update_frequency The rate at which data refreshes on this plan
     * @param array<string, bool> $features The supported features of this plan (base, symbols, experimental, time-series, convert)
     */
    public function __construct(
        public string $name,
        public string $quota,
        public string $update_frequency,
        public array $features,
    )
    {
    }
}