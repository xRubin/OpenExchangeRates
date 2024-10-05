<?php declare(strict_types=1);

namespace OpenExchangeRates;

readonly class UsageData
{
    /**
     * @param string $app_id The app ID you provided
     * @param string $status The current status of this app ID (either 'active' or 'access_restricted')
     * @param PlanInformation $plan Plan information for this app ID
     * @param UsageInformation $usage Usage information for this app ID
     */
    public function __construct(
        public string           $app_id,
        public string           $status,
        public PlanInformation  $plan,
        public UsageInformation $usage
    )
    {
    }
}