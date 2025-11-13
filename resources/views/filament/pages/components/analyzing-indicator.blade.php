<div class="flex flex-col items-center justify-center py-12">
    <div class="relative">
        <svg class="w-16 h-16 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <x-filament::icon icon="heroicon-o-sparkles" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
        </div>
    </div>
    <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-gray-100">
        Analyzing Your CV...
    </h3>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center max-w-md">
        Our AI is comparing your CV against the job description. This usually takes 5-10 seconds.
    </p>
    <div class="mt-6 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
        <span>Analyzing...</span>
    </div>
</div>
