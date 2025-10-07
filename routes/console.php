<?php

use App\Services\MetricsCalculationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('metrics:refresh', function (MetricsCalculationService $service) {
    $this->info('Refreshing metrics...');
    $service->refreshAllMetrics('30d');
    $this->info('Metrics refreshed successfully!');
})->purpose('Refresh application metrics for the last 30 days');

Schedule::command('metrics:refresh')->daily();
Schedule::command('review:check-budget')->daily();
