<?php declare(strict_types=1);

namespace OpenExchangeRates;

readonly class UsageInformation
{
    /**
     * @param int $requests Number of requests made since month start
     * @param int $requests_quota Number of requests allowed each month with this plan
     * @param int $requests_remaining Number of requests remaining this month
     * @param int $days_elapsed Number of days since start of month
     * @param int $days_remaining Number of days remaining until next month's start
     * @param int $daily_average Average requests per day
     */
    public function __construct(
        public int $requests,
        public int $requests_quota,
        public int $requests_remaining,
        public int $days_elapsed,
        public int $days_remaining,
        public int $daily_average,
    )
    {
    }
}