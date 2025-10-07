@php
    $skillGaps = $record->ai_review_data['skill_gaps'] ?? [];
@endphp

<div class="space-y-3">
    @forelse($skillGaps as $gap)
        <div class="rounded-lg border @if(($gap['priority'] ?? 'medium') === 'high') border-danger-200 dark:border-danger-800 @elseif(($gap['priority'] ?? 'medium') === 'medium') border-warning-200 dark:border-warning-800 @else border-gray-200 dark:border-gray-700 @endif p-4">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $gap['skill'] ?? 'Unknown Skill' }}</h4>
                @if(isset($gap['priority']))
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium @if($gap['priority'] === 'high') bg-danger-50 text-danger-700 dark:bg-danger-900/20 dark:text-danger-300 @elseif($gap['priority'] === 'medium') bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-300 @else bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                        {{ ucfirst($gap['priority']) }} Priority
                    </span>
                @endif
            </div>
            @if(isset($gap['reason']))
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $gap['reason'] }}</p>
            @endif
            @if(isset($gap['suggestion']))
                <div class="mt-2 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-md">
                    <p class="text-sm font-medium text-primary-900 dark:text-primary-100">💡 Suggestion:</p>
                    <p class="text-sm text-primary-800 dark:text-primary-200 mt-1">{{ $gap['suggestion'] }}</p>
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">No skill gaps identified.</p>
    @endforelse
</div>
