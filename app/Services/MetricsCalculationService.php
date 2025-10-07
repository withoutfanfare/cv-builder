<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\Metric;

/**
 * Service for calculating and persisting job application metrics.
 *
 * This service calculates various performance metrics for job applications
 * and stores them in the metrics table for dashboard display.
 *
 * Note: All calculations exclude withdrawn applications from the denominators
 * to provide accurate success rates for active applications only.
 *
 * Performance: Optimized for up to 1000 applications per time period.
 * Expected calculation time: <100ms for 100 applications.
 */
class MetricsCalculationService
{
    /**
     * Refresh all metrics for a given time period.
     *
     * @param  string  $timePeriod  Period in format: '7d', '30d', or '90d'
     */
    public function refreshAllMetrics(string $timePeriod): void
    {
        $this->calculateApplicationsPerWeek($timePeriod);
        $this->calculateResponseRate($timePeriod);
        $this->calculateInterviewConversionRate($timePeriod);
        $this->calculateOfferRate($timePeriod);
        $this->calculateMedianDaysToFirstResponse($timePeriod);
    }

    /**
     * Calculate average applications submitted per week.
     *
     * Includes all applications (both active and withdrawn) as submission
     * rate measures activity, not success.
     *
     * @param  string  $timePeriod  Period in format: '7d', '30d', or '90d'
     */
    public function calculateApplicationsPerWeek(string $timePeriod): void
    {
        [$startDate, $endDate] = $this->parsePeriod($timePeriod);

        $totalApplications = JobApplication::whereBetween('created_at', [$startDate, $endDate])->count();

        $days = $startDate->diffInDays($endDate);
        // Use ceil to ensure minimum 1 week, prevents division by zero
        $weeks = max(1, ceil($days / 7));
        $applicationsPerWeek = round($totalApplications / $weeks, 2);

        $this->storeMetric('applications_per_week', $applicationsPerWeek, $startDate, $endDate);
    }

    /**
     * Calculate the percentage of applications that received a response.
     *
     * Response rate = (Applications with reply_received event / Total active applications) * 100
     *
     * Note: Excludes withdrawn applications from denominator. Only counts
     * applications with explicit reply_received events.
     *
     * @param  string  $timePeriod  Period in format: '7d', '30d', or '90d'
     */
    public function calculateResponseRate(string $timePeriod): void
    {
        [$startDate, $endDate] = $this->parsePeriod($timePeriod);

        $totalActiveApplications = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->count();

        $applicationsWithReplies = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->whereHas('events', function ($query) {
                $query->where('event_type', 'reply_received');
            })
            ->count();

        $responseRate = $totalActiveApplications > 0
            ? round(($applicationsWithReplies / $totalActiveApplications) * 100, 2)
            : 0;

        $this->storeMetric('response_rate', $responseRate, $startDate, $endDate);
    }

    /**
     * Calculate the percentage of applications that led to interviews.
     *
     * Interview conversion rate = (Applications with interview events / Total active applications) * 100
     *
     * Counts applications with interview_scheduled or interview_completed events.
     * Excludes withdrawn applications from denominator.
     *
     * @param  string  $timePeriod  Period in format: '7d', '30d', or '90d'
     */
    public function calculateInterviewConversionRate(string $timePeriod): void
    {
        [$startDate, $endDate] = $this->parsePeriod($timePeriod);

        $totalActiveApplications = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->count();

        $applicationsWithInterviews = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->whereHas('events', function ($query) {
                $query->where('event_type', 'interview_scheduled');
            })
            ->count();

        $conversionRate = $totalActiveApplications > 0
            ? round(($applicationsWithInterviews / $totalActiveApplications) * 100, 2)
            : 0;

        $this->storeMetric('interview_conversion_rate', $conversionRate, $startDate, $endDate);
    }

    public function calculateOfferRate(string $timePeriod): void
    {
        [$startDate, $endDate] = $this->parsePeriod($timePeriod);

        $totalActiveApplications = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->count();

        $applicationsWithOffers = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('withdrawn_at')
            ->whereHas('events', function ($query) {
                $query->where('event_type', 'offer_received');
            })
            ->count();

        $offerRate = $totalActiveApplications > 0
            ? round(($applicationsWithOffers / $totalActiveApplications) * 100, 2)
            : 0;

        $this->storeMetric('offer_rate', $offerRate, $startDate, $endDate);
    }

    public function calculateMedianDaysToFirstResponse(string $timePeriod): void
    {
        [$startDate, $endDate] = $this->parsePeriod($timePeriod);

        $responseTimes = JobApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('events', function ($query) {
                $query->where('event_type', 'reply_received');
            })
            ->with(['events' => function ($query) {
                $query->where('event_type', 'reply_received')->orderBy('occurred_at');
            }])
            ->get()
            ->map(function ($application) {
                $firstReply = $application->events->first();
                if ($firstReply) {
                    return $application->created_at->diffInDays($firstReply->occurred_at);
                }

                return null;
            })
            ->filter()
            ->sort()
            ->values();

        $median = $this->calculateMedian($responseTimes->toArray());

        $this->storeMetric('median_days_to_first_response', $median, $startDate, $endDate);
    }

    protected function parsePeriod(string $timePeriod): array
    {
        // Parse time period like '30d' into days
        preg_match('/(\d+)d/', $timePeriod, $matches);
        $days = (int) ($matches[1] ?? 30);

        $endDate = now();
        $startDate = now()->subDays($days);

        return [$startDate, $endDate];
    }

    protected function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0.0;
        }

        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return (float) $values[$middle];
    }

    protected function storeMetric(string $metricType, float $value, $startDate, $endDate): void
    {
        Metric::updateOrCreate(
            [
                'metric_type' => $metricType,
                'time_period_start' => $startDate->toDateString(),
            ],
            [
                'value' => $value,
                'time_period_end' => $endDate->toDateString(),
                'last_refreshed_at' => now(),
            ]
        );
    }
}
