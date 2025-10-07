@php
    $suggestions = $record->ai_review_data['language_suggestions'] ?? [];
@endphp

<div class="space-y-3">
    @forelse($suggestions as $suggestion)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            @if(isset($suggestion['category']) || isset($suggestion['impact']))
                <div class="flex justify-between items-start mb-2">
                    @if(isset($suggestion['category']))
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $suggestion['category'] }}</span>
                    @endif
                    @if(isset($suggestion['impact']))
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium @if($suggestion['impact'] === 'high') bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-300 @elseif($suggestion['impact'] === 'medium') bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-300 @else bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                            {{ ucfirst($suggestion['impact']) }} Impact
                        </span>
                    @endif
                </div>
            @endif

            @if(isset($suggestion['generic_term']) && isset($suggestion['job_specific_term']))
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Generic:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $suggestion['generic_term'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-primary-700 dark:text-primary-300 mb-1">Job-Specific:</p>
                        <p class="text-sm font-medium text-primary-900 dark:text-primary-100">{{ $suggestion['job_specific_term'] }}</p>
                    </div>
                </div>
            @endif

            @if(isset($suggestion['original']))
                <div class="p-3 bg-gray-50 dark:bg-gray-900/20 rounded-md mb-2">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Original:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $suggestion['original'] }}</p>
                </div>
            @endif

            @if(isset($suggestion['suggestion']))
                <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-md">
                    <p class="text-xs font-medium text-primary-700 dark:text-primary-300 mb-1">💡 Suggestion:</p>
                    <p class="text-sm text-primary-900 dark:text-primary-100">{{ $suggestion['suggestion'] }}</p>
                </div>
            @endif

            @if(isset($suggestion['context_location']))
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">📍 {{ $suggestion['context_location'] }}</p>
            @endif

            @if(isset($suggestion['example']))
                <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-900/20 rounded-md">
                    <p class="text-xs text-gray-600 dark:text-gray-400 italic">Example: {{ $suggestion['example'] }}</p>
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">Language looks well-aligned with the job!</p>
    @endforelse
</div>
