@php
    $recommendations = $record->ai_review_data['section_recommendations'] ?? [];
@endphp

<div class="space-y-3">
    @forelse($recommendations as $rec)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $rec['section'] ?? $rec['section_type'] ?? 'Section') }}</h4>
                @if(isset($rec['priority']) || isset($rec['impact']))
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium @if(($rec['priority'] ?? $rec['impact'] ?? 'medium') === 'high') bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-300 @elseif(($rec['priority'] ?? $rec['impact'] ?? 'medium') === 'medium') bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-300 @else bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                        {{ ucfirst($rec['priority'] ?? $rec['impact'] ?? 'medium') }} Priority
                    </span>
                @endif
            </div>
            @if(isset($rec['current_priority']) && isset($rec['suggested_priority']))
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>Current Position: #{{ $rec['current_priority'] }}</span>
                    <x-heroicon-s-arrow-right class="h-4 w-4" />
                    <span class="font-medium">Suggested: #{{ $rec['suggested_priority'] }}</span>
                </div>
            @endif
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $rec['recommendation'] ?? $rec['rationale'] ?? 'No details provided' }}</p>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">Section ordering looks good!</p>
    @endforelse
</div>
