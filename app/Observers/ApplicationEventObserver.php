<?php

namespace App\Observers;

use App\Models\ApplicationEvent;

class ApplicationEventObserver
{
    /**
     * Handle the ApplicationEvent "created" event.
     */
    public function created(ApplicationEvent $event): void
    {
        // Use saveQuietly to prevent infinite observer loops
        $jobApp = $event->jobApplication;
        $jobApp->last_activity_at = $event->occurred_at ?? now();
        $jobApp->saveQuietly();
    }
}
