<?php

namespace App\Filament\Widgets;

use App\Models\Metric;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class MetricsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    // Poll for updates every 5 minutes instead of every page load
    protected static ?string $pollingInterval = '5m';

    protected function getStats(): array
    {
        // Cache metrics for 5 minutes to reduce database queries
        $metrics = Cache::remember('dashboard_metrics', 300, function () {
            return Metric::select('metric_type', 'value', 'last_refreshed_at', 'time_period_start')
                ->whereIn('metric_type', [
                    'applications_per_week',
                    'response_rate',
                    'interview_conversion_rate',
                    'offer_rate',
                    'median_days_to_first_response',
                ])
                ->latest('time_period_start')
                ->get()
                ->groupBy('metric_type')
                ->map(fn ($group) => $group->first());
        });

        $applicationsPerWeek = $metrics->get('applications_per_week');
        $responseRate = $metrics->get('response_rate');
        $interviewConversionRate = $metrics->get('interview_conversion_rate');
        $offerRate = $metrics->get('offer_rate');
        $medianDaysToFirstResponse = $metrics->get('median_days_to_first_response');

        return [
            Stat::make('Applications per Week', $applicationsPerWeek?->value ?? 0)
                ->description($this->getMetricDescription($applicationsPerWeek))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Response Rate', ($responseRate?->value ?? 0).'%')
                ->description($this->getMetricDescription($responseRate))
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),

            Stat::make('Interview Conversion', ($interviewConversionRate?->value ?? 0).'%')
                ->description($this->getMetricDescription($interviewConversionRate))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Offer Rate', ($offerRate?->value ?? 0).'%')
                ->description($this->getMetricDescription($offerRate))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make('Median Days to Response', round($medianDaysToFirstResponse?->value ?? 0))
                ->description($this->getMetricDescription($medianDaysToFirstResponse))
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),
        ];
    }

    protected function getMetricDescription(?Metric $metric): string
    {
        if (! $metric) {
            return 'No data available';
        }

        return 'Last 30 days • Updated '.$metric->last_refreshed_at->diffForHumans();
    }
}
