<div class="space-y-4">
    @if($getRecord()?->ai_review_requested_at && !$getRecord()?->ai_review_completed_at)
        <div class="rounded-lg bg-warning-50 p-4 dark:bg-warning-900/20">
            <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400">
                <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-medium">⏳ Review in progress...</span>
            </div>
            <p class="mt-2 text-sm text-warning-600 dark:text-warning-400">
                Your CV review is being processed. This usually takes 5-10 seconds. Refresh the page to see results.
            </p>
        </div>
    @endif

    @if($getRecord()?->ai_review_completed_at)
        {{-- Stale Review Warning --}}
        @if($getRecord()->isReviewStale())
            <div class="rounded-lg bg-warning-50 p-4 dark:bg-warning-900/20">
                <div class="flex items-center gap-2 text-warning-700 dark:text-warning-300">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5" />
                    <span class="font-medium">Review Out of Date</span>
                </div>
                <p class="mt-1 text-sm text-warning-600 dark:text-warning-400">
                    Your CV has been modified since this review was generated. Consider regenerating the review for up-to-date recommendations.
                </p>
            </div>
        @endif

        {{-- Match Score and Cost --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Match Score</span>
                <div class="mt-1">
                    @php
                        $score = $getRecord()->ai_review_data['match_score'] ?? 0;
                        $color = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                    @endphp
                    <x-filament::badge color="{{ $color }}" size="lg">
                        {{ $score }}%
                    </x-filament::badge>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cost</span>
                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    ${{ number_format(($getRecord()->ai_review_cost_cents ?? 0) / 100, 2) }}
                </div>
            </div>
        </div>

        {{-- Tabs for Review Sections --}}
        <div x-data="{ activeTab: 'skill-gaps' }" class="mt-4">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @php
                        $tabs = [
                            'skill-gaps' => ['label' => 'Skill Gaps', 'icon' => 'heroicon-o-academic-cap', 'count' => count($getRecord()->ai_review_data['skill_gaps'] ?? [])],
                            'section-priority' => ['label' => 'Section Priority', 'icon' => 'heroicon-o-bars-3', 'count' => count($getRecord()->ai_review_data['section_recommendations'] ?? [])],
                            'bullet-points' => ['label' => 'Bullet Points', 'icon' => 'heroicon-o-list-bullet', 'count' => count($getRecord()->ai_review_data['bullet_improvements'] ?? [])],
                            'language' => ['label' => 'Language', 'icon' => 'heroicon-o-language', 'count' => count($getRecord()->ai_review_data['language_suggestions'] ?? [])],
                            'skill-evidence' => ['label' => 'Skill Evidence', 'icon' => 'heroicon-o-check-badge', 'count' => count($getRecord()->ai_review_data['skill_evidence'] ?? [])],
                        ];
                    @endphp
                    @foreach($tabs as $tabId => $tab)
                        <button
                            @click="activeTab = '{{ $tabId }}'"
                            :class="activeTab === '{{ $tabId }}' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="flex items-center gap-2 border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                        >
                            <x-filament::icon :icon="$tab['icon']" class="h-4 w-4" />
                            {{ $tab['label'] }}
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-800">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="mt-4">
                {{-- Skill Gaps Tab --}}
                <div x-show="activeTab === 'skill-gaps'" x-cloak>
                    @include('filament.infolists.skill-gaps', ['record' => $getRecord()])
                </div>

                {{-- Section Priority Tab --}}
                <div x-show="activeTab === 'section-priority'" x-cloak>
                    @include('filament.infolists.section-recommendations', ['record' => $getRecord()])
                </div>

                {{-- Bullet Points Tab --}}
                <div x-show="activeTab === 'bullet-points'" x-cloak>
                    @include('filament.infolists.bullet-improvements', ['record' => $getRecord()])
                </div>

                {{-- Language Tab --}}
                <div x-show="activeTab === 'language'" x-cloak>
                    @include('filament.infolists.language-suggestions', ['record' => $getRecord()])
                </div>

                {{-- Skill Evidence Tab --}}
                <div x-show="activeTab === 'skill-evidence'" x-cloak>
                    @include('filament.infolists.skill-evidence', ['record' => $getRecord()])
                </div>
            </div>
        </div>

        {{-- Action Checklist --}}
        @if(!empty($getRecord()->ai_review_data['action_checklist']))
            <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <h4 class="mb-2 font-medium text-gray-900 dark:text-gray-100">Action Items</h4>
                @include('filament.infolists.action-checklist', ['record' => $getRecord()])
            </div>
        @endif
    @endif
</div>
