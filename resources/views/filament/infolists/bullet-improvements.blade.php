@php
    $improvements = $record->ai_review_data['bullet_improvements'] ?? [];
@endphp

<div class="space-y-4">
    @forelse($improvements as $improvement)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            @if(isset($improvement['section']))
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $improvement['section'] }}</span>
                    @if(isset($improvement['priority']))
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium @if($improvement['priority'] === 'emphasize') bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-300 @elseif($improvement['priority'] === 'keep') bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-300 @elseif($improvement['priority'] === 'de-emphasize') bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-300 @else bg-danger-50 text-danger-700 dark:bg-danger-900/20 dark:text-danger-300 @endif">
                            {{ ucfirst($improvement['priority']) }}
                        </span>
                    @endif
                </div>
            @endif

            <div class="space-y-2">
                @if(isset($improvement['bullet']) || isset($improvement['original_bullet']))
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/20 rounded-md">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Current:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $improvement['bullet'] ?? $improvement['original_bullet'] }}</p>
                    </div>
                @endif

                @if(isset($improvement['improvement']) || isset($improvement['suggested_bullet']))
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-md">
                        <p class="text-xs font-medium text-primary-700 dark:text-primary-300 mb-1">💡 Improvement:</p>
                        <p class="text-sm text-primary-900 dark:text-primary-100">{{ $improvement['improvement'] ?? $improvement['suggested_bullet'] }}</p>
                    </div>
                @endif
            </div>

            @if(isset($improvement['reason']))
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic">{{ $improvement['reason'] }}</p>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">No bullet point improvements suggested.</p>
    @endforelse
</div>
