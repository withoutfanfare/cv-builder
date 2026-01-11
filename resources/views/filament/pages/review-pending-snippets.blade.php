<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg bg-gradient-to-r from-warning-500/10 to-orange-500/10 border border-warning-500/20 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-warning-500 to-orange-500 flex items-center justify-center">
                        <x-filament::icon icon="heroicon-o-eye" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Review AI-Generated Snippets
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        AI has extracted and categorized achievement bullets from your CV. Review the generated snippets below,
                        then save the ones you want to keep or save all to your library.
                    </p>
                </div>
            </div>
        </div>

        @php
            $pendingCount = count(session('pending_snippets', []));
        @endphp

        @if($pendingCount > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <div class="text-3xl font-bold text-warning-500">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Snippets Generated</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <div class="text-3xl font-bold text-primary-500">
                        {{ collect(session('pending_snippets', []))->unique('category')->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Categories</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <div class="text-3xl font-bold text-success-500">
                        {{ collect(session('pending_snippets', []))->pluck('tags')->flatten()->unique()->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unique Tags</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <div class="text-3xl font-bold text-info-500">
                        {{ collect(session('pending_snippets', []))->unique('role_type')->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Role Types</div>
                </div>
            </div>
        @endif

        {{ $this->table }}
    </div>
</x-filament-panels::page>
