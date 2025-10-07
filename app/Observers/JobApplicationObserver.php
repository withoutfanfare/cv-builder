<?php

namespace App\Observers;

use App\Models\JobApplication;
use App\Services\PdfSnapshotService;

class JobApplicationObserver
{
    /**
     * Handle the JobApplication "updating" event.
     * Auto-update last_activity_at timestamp only if not explicitly set
     */
    public function updating(JobApplication $jobApplication): void
    {
        if (! $jobApplication->isDirty('last_activity_at')) {
            $jobApplication->last_activity_at = now();
        }
    }

    /**
     * Handle the JobApplication "updated" event.
     * Trigger PDF snapshot creation when send_status changes to 'sent'
     */
    public function updated(JobApplication $jobApplication): void
    {
        // Check if send_status changed to 'sent' and no snapshot exists yet
        if ($jobApplication->wasChanged('send_status') &&
            $jobApplication->send_status === 'sent' &&
            ! $jobApplication->pdfSnapshot) {

            try {
                $pdfSnapshotService = app(PdfSnapshotService::class);
                $pdfSnapshotService->create($jobApplication);
            } catch (\Exception $e) {
                // Log error but don't fail the update
                logger()->error('Failed to create PDF snapshot', [
                    'job_application_id' => $jobApplication->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
