<?php

namespace App\Jobs;

use App\Models\BatchAnalysis;
use App\Models\JobApplication;
use App\Services\CvReviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBatchAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    public $tries = 1; // Don't retry to avoid double charges

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BatchAnalysis $batchAnalysis
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CvReviewService $reviewService): void
    {
        try {
            $this->batchAnalysis->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            $cv = $this->batchAnalysis->cv;
            $jobDescriptions = $this->batchAnalysis->job_descriptions;
            $results = [];
            $totalCost = 0;

            foreach ($jobDescriptions as $index => $jobData) {
                try {
                    // Create temporary job application for analysis
                    $tempJobApp = new JobApplication([
                        'job_description' => $jobData['description'],
                        'company_name' => $jobData['company_name'] ?? "Job #{$index + 1}",
                        'job_title' => $jobData['job_title'] ?? 'Position',
                        'cv_id' => $cv->id,
                    ]);

                    // Perform AI analysis
                    $analysis = $reviewService->analyzeForJob($cv, $tempJobApp);

                    // Estimate cost
                    $tokens = $reviewService->estimateTokenCount($cv, $tempJobApp);
                    $cost = $reviewService->estimateCostCents($tokens);
                    $totalCost += $cost;

                    // Store result
                    $results[] = [
                        'index' => $index,
                        'company_name' => $jobData['company_name'] ?? "Job #{$index + 1}",
                        'job_title' => $jobData['job_title'] ?? 'Position',
                        'match_score' => $analysis['match_score'] ?? 0,
                        'skill_gaps' => $analysis['skill_gaps'] ?? [],
                        'section_recommendations' => $analysis['section_recommendations'] ?? [],
                        'action_checklist' => $analysis['action_checklist'] ?? [],
                        'cost_cents' => $cost,
                    ];
                } catch (\Exception $e) {
                    Log::error('Batch analysis job failed', [
                        'index' => $index,
                        'error' => $e->getMessage(),
                    ]);

                    $results[] = [
                        'index' => $index,
                        'company_name' => $jobData['company_name'] ?? "Job #{$index + 1}",
                        'job_title' => $jobData['job_title'] ?? 'Position',
                        'match_score' => 0,
                        'error' => $e->getMessage(),
                        'cost_cents' => 0,
                    ];
                }
            }

            $this->batchAnalysis->update([
                'status' => 'completed',
                'completed_at' => now(),
                'results' => $results,
                'total_cost_cents' => $totalCost,
            ]);
        } catch (\Exception $e) {
            Log::error('Batch analysis failed', [
                'batch_id' => $this->batchAnalysis->id,
                'error' => $e->getMessage(),
            ]);

            $this->batchAnalysis->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
