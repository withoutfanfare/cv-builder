<?php

namespace App\Providers;

use App\Models\ApplicationEvent;
use App\Models\JobApplication;
use App\Observers\ApplicationEventObserver;
use App\Observers\JobApplicationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JobApplication::observe(JobApplicationObserver::class);
        ApplicationEvent::observe(ApplicationEventObserver::class);
        \App\Models\PdfTemplate::observe(\App\Observers\PdfTemplateObserver::class);
    }
}
