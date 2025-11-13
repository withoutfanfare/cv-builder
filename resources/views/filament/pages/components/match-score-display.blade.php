@php
    $scoreColor = match($color) {
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
        'danger' => 'text-danger-500',
        default => 'text-gray-500',
    };

    $bgColor = match($color) {
        'success' => 'from-success-500/20 to-success-600/10',
        'warning' => 'from-warning-500/20 to-warning-600/10',
        'danger' => 'from-danger-500/20 to-danger-600/10',
        default => 'from-gray-500/20 to-gray-600/10',
    };
@endphp

<div class="rounded-lg bg-gradient-to-br {{ $bgColor }} border border-{{ $color }}-500/30 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">CV Match Score</p>
            <p class="text-5xl font-bold {{ $scoreColor }}">{{ $score }}%</p>
        </div>
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-{{ $color }}-500 to-{{ $color }}-600 flex items-center justify-center">
            <x-filament::icon
                :icon="$score >= 70 ? 'heroicon-o-check-circle' : ($score >= 50 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-x-circle')"
                class="w-12 h-12 text-white"
            />
        </div>
    </div>
</div>
