<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\CvReviewService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCvReview implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(public JobApplication $jobApplication)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CvReviewService $service): void
    {
        try {
            $reviewData = $service->analyzeForJob(
                $this->jobApplication->cv,
                $this->jobApplication
            );

            $estimatedTokens = $reviewData['analysis_metadata']['tokens_used'] ?? 0;
            $costCents = $service->estimateCostCents($estimatedTokens);

            $this->jobApplication->update([
                'ai_review_data' => $reviewData,
                'ai_review_completed_at' => now(),
                'ai_review_cost_cents' => $costCents,
            ]);

            Log::info('CV review completed successfully', [
                'job_application_id' => $this->jobApplication->id,
                'match_score' => $reviewData['match_score'],
                'cost_cents' => $costCents,
            ]);
        } catch (\Exception $e) {
            Log::error('CV review failed', [
                'job_application_id' => $this->jobApplication->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
