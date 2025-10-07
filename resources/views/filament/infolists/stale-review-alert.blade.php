<div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 p-4 mb-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-warning-400" />
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-warning-800 dark:text-warning-200">
                Review Out of Date
            </h3>
            <div class="mt-2 text-sm text-warning-700 dark:text-warning-300">
                <p>
                    Your CV was updated {{ $getRecord()->cv->updated_at->diffForHumans() }},
                    after this review was generated. Consider regenerating the review for updated suggestions.
                </p>
            </div>
        </div>
    </div>
</div>
