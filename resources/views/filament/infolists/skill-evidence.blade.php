@php
    $evidence = $record->ai_review_data['skill_evidence'] ?? [];
@endphp

<div class="space-y-3">
    @forelse($evidence as $item)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $item['skill'] ?? 'Unknown Skill' }}</h4>
                @if(isset($item['improvement_priority']))
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium @if($item['improvement_priority'] === 'high') bg-danger-50 text-danger-700 dark:bg-danger-900/20 dark:text-danger-300 @elseif($item['improvement_priority'] === 'medium') bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-300 @else bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                        {{ ucfirst($item['improvement_priority']) }} Priority
                    </span>
                @endif
            </div>

            @if(isset($item['current_strength']) || isset($item['evidence_quality']))
                <div class="grid grid-cols-2 gap-3 mb-2">
                    @if(isset($item['current_strength']))
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Current Strength:</p>
                            <p class="text-sm capitalize">{{ $item['current_strength'] }}</p>
                        </div>
                    @endif
                    @if(isset($item['evidence_quality']))
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Evidence Quality:</p>
                            <p class="text-sm capitalize">{{ $item['evidence_quality'] }}</p>
                        </div>
                    @endif
                </div>
            @endif

            @if(isset($item['evidence']) && is_array($item['evidence']))
                <div class="mt-2 p-3 bg-success-50 dark:bg-success-900/20 rounded-md">
                    <p class="text-sm font-medium text-success-900 dark:text-success-100 mb-2">✓ Evidence Found:</p>
                    <ul class="space-y-1">
                        @foreach($item['evidence'] as $ev)
                            <li class="text-sm text-success-800 dark:text-success-200">• {{ $ev }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(isset($item['suggestion']))
                <div class="mt-2 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-md">
                    <p class="text-sm font-medium text-primary-900 dark:text-primary-100">💡 How to Strengthen:</p>
                    <p class="text-sm text-primary-800 dark:text-primary-200 mt-1">{{ $item['suggestion'] }}</p>
                </div>
            @endif

            @if(isset($item['related_experience_ids']) && !empty($item['related_experience_ids']))
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                    Related experiences: {{ implode(', ', $item['related_experience_ids']) }}
                </p>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">All skills are well-evidenced!</p>
    @endforelse
</div>
