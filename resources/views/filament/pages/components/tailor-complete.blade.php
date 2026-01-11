<div class="flex flex-col items-center justify-center py-12">
    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-success-500 to-success-600 flex items-center justify-center mb-6">
        <x-filament::icon icon="heroicon-o-check-badge" class="w-12 h-12 text-white" />
    </div>

    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
        Your CV Has Been Tailored!
    </h2>

    <p class="text-center text-gray-600 dark:text-gray-400 max-w-md mb-8">
        All improvements have been applied. A new job application has been created with your tailored CV.
        You can now download the PDF or make additional adjustments.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full max-w-2xl">
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <x-filament::icon icon="heroicon-o-check-circle" class="w-8 h-8 mx-auto text-success-500 mb-2" />
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Improvements Applied</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Language enhanced</p>
        </div>

        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <x-filament::icon icon="heroicon-o-document-text" class="w-8 h-8 mx-auto text-primary-500 mb-2" />
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Application Created</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ready to track</p>
        </div>

        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
            <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-8 h-8 mx-auto text-info-500 mb-2" />
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">PDF Ready</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Download anytime</p>
        </div>
    </div>
</div>
