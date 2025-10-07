@php
    $checklist = $record->ai_review_data['action_checklist'] ?? [];
@endphp

<ol class="space-y-2">
    @foreach($checklist as $index => $action)
        @php
            // Handle both array format and string format
            $actionText = is_array($action) ? ($action['action'] ?? $action['description'] ?? '') : $action;
            $priority = is_array($action) ? ($action['priority'] ?? 'medium') : 'medium';
        @endphp
        @if($actionText)
            <li class="flex items-start gap-3 p-3 rounded-lg @if($priority === 'high') bg-danger-50 dark:bg-danger-900/20 @elseif($priority === 'medium') bg-warning-50 dark:bg-warning-900/20 @else bg-gray-50 dark:bg-gray-900/20 @endif">
                <div class="flex-shrink-0 w-6 h-6 rounded-full @if($priority === 'high') bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300 @elseif($priority === 'medium') bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300 @else bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300 @endif flex items-center justify-center text-sm font-medium">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $actionText }}</p>
                    @if(is_array($action) && isset($action['priority']))
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium mt-1 @if($priority === 'high') bg-danger-100 text-danger-700 dark:bg-danger-900/50 dark:text-danger-300 @elseif($priority === 'medium') bg-warning-100 text-warning-700 dark:bg-warning-900/50 dark:text-warning-300 @else bg-gray-100 text-gray-700 dark:bg-gray-900/50 dark:text-gray-300 @endif">
                            {{ ucfirst($priority) }} Priority
                        </span>
                    @endif
                </div>
            </li>
        @endif
    @endforeach
</ol>
