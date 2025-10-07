<?php

namespace App\Filament\Widgets;

use App\Models\Metric;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MetricsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $applicationsPerWeek = Metric::where('metric_type', 'applications_per_week')
            ->latest('time_period_start')
            ->first();

        $responseRate = Metric::where('metric_type', 'response_rate')
            ->latest('time_period_start')
            ->first();

        $interviewConversionRate = Metric::where('metric_type', 'interview_conversion_rate')
            ->latest('time_period_start')
            ->first();

        $offerRate = Metric::where('metric_type', 'offer_rate')
            ->latest('time_period_start')
            ->first();

        $medianDaysToFirstResponse = Metric::where('metric_type', 'median_days_to_first_response')
            ->latest('time_period_start')
            ->first();

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
