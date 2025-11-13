<?php

use App\Services\MetricsCalculationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('metrics:refresh', function (MetricsCalculationService $service) {
    $this->info('Refreshing metrics...');

    // Clear the dashboard metrics cache before refreshing
    Cache::forget('dashboard_metrics');

    $service->refreshAllMetrics('30d');

    $this->info('Metrics refreshed successfully!');
    $this->info('Dashboard cache cleared.');
})->purpose('Refresh application metrics for the last 30 days');

Schedule::command('metrics:refresh')->daily();
Schedule::command('review:check-budget')->daily();
