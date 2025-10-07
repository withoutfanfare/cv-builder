<?php

namespace App\Console\Commands;

use App\Models\JobApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckReviewBudget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'review:check-budget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check monthly AI review budget and alert if threshold exceeded';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $budgetCents = config('services.openai.monthly_budget_cents', 5000);
        $threshold = 0.8; // 80% threshold

        // Calculate total cost this month
        $totalCostCents = JobApplication::query()
            ->whereNotNull('ai_review_completed_at')
            ->whereYear('ai_review_completed_at', now()->year)
            ->whereMonth('ai_review_completed_at', now()->month)
            ->sum('ai_review_cost_cents');

        $percentageUsed = $budgetCents > 0 ? ($totalCostCents / $budgetCents) * 100 : 0;

        $this->info('Monthly AI Review Budget Status:');
        $this->info('Budget: $'.number_format($budgetCents / 100, 2));
        $this->info('Used: $'.number_format($totalCostCents / 100, 2).' ('.round($percentageUsed, 1).'%)');
        $this->info('Remaining: $'.number_format(($budgetCents - $totalCostCents) / 100, 2));

        if ($percentageUsed >= ($threshold * 100)) {
            $message = 'AI Review budget at '.round($percentageUsed, 1).'% ($'.number_format($totalCostCents / 100, 2).' of $'.number_format($budgetCents / 100, 2).')';

            $this->warn($message);

            Log::warning('AI Review budget threshold exceeded', [
                'budget_cents' => $budgetCents,
                'used_cents' => $totalCostCents,
                'percentage' => $percentageUsed,
            ]);

            // In production, you would send a notification here
            // Notification::route('mail', config('mail.admin_email'))
            //     ->notify(new BudgetThresholdExceeded($totalCostCents, $budgetCents));
        }

        return self::SUCCESS;
    }
}
