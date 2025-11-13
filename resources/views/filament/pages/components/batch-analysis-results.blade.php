@php
    $results = $record->getSortedResults();
    $avgScore = round($record->getAverageScore());
    $bestMatch = $record->getBestMatch();
@endphp

<div class="space-y-6">
    @if($record->isProcessing())
        <div class="flex flex-col items-center justify-center py-12">
            <svg class="w-12 h-12 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Processing {{ count($record->job_descriptions) }} job descriptions...</p>
        </div>
    @elseif($record->hasFailed())
        <div class="rounded-lg bg-danger-50 dark:bg-danger-900/20 p-6 text-center">
            <x-filament::icon icon="heroicon-o-x-circle" class="w-12 h-12 mx-auto text-danger-500 mb-2" />
            <p class="font-medium text-danger-900 dark:text-danger-100">Analysis Failed</p>
            <p class="text-sm text-danger-700 dark:text-danger-300 mt-1">Please try again or contact support</p>
        </div>
    @elseif($record->isComplete())
        {{-- Summary Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ count($results) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Jobs Analyzed</div>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                <div class="text-3xl font-bold {{ $avgScore >= 70 ? 'text-success-500' : ($avgScore >= 50 ? 'text-warning-500' : 'text-danger-500') }}">
                    {{ $avgScore }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Average Score</div>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($record->total_cost_cents / 100, 2) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Cost</div>
            </div>
        </div>

        {{-- Best Match Highlight --}}
        @if($bestMatch)
            <div class="rounded-lg bg-gradient-to-r from-success-500/10 to-success-600/10 border border-success-500/30 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-success-500 flex items-center justify-center">
                            <x-filament::icon icon="heroicon-o-trophy" class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-success-900 dark:text-success-100 mb-1">Best Match</h3>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $bestMatch['company_name'] }} - {{ $bestMatch['job_title'] }}
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-2xl font-bold text-success-600">{{ $bestMatch['match_score'] }}%</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">match score</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detailed Results --}}
        <div class="space-y-3">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Detailed Results</h3>

            @foreach($results as $result)
                @php
                    $score = $result['match_score'] ?? 0;
                    $scoreColor = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                @endphp

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $result['company_name'] }} - {{ $result['job_title'] }}
                            </h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-bold text-{{ $scoreColor }}-600">{{ $score }}%</span>
                            <span class="text-xs text-gray-500">${{ number_format(($result['cost_cents'] ?? 0) / 100, 2) }}</span>
                        </div>
                    </div>

                    @if(!empty($result['skill_gaps']))
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Top Skill Gaps:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($result['skill_gaps'], 0, 5) as $gap)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/20 dark:text-danger-300">
                                        {{ $gap['skill'] ?? 'Unknown' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($result['error']))
                        <div class="mt-3 text-sm text-danger-600 dark:text-danger-400">
                            Error: {{ $result['error'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
