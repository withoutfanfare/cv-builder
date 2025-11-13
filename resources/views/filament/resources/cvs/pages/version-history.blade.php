<div class="space-y-4">
    @if($versions->isEmpty())
        <div class="text-center py-12">
            <x-filament::icon icon="heroicon-o-clock" class="w-12 h-12 mx-auto text-gray-400 mb-3" />
            <p class="text-gray-600 dark:text-gray-400">No version history available</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Versions are created when you clone a CV</p>
        </div>
    @else
        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 mb-4">
            <div class="flex items-start gap-3">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                <div class="flex-1 text-sm">
                    <p class="font-medium text-blue-900 dark:text-blue-100 mb-1">About Version History</p>
                    <p class="text-blue-700 dark:text-blue-300">
                        Versions are snapshots of your CV at specific points in time. They're created automatically when you clone a CV or can be created manually for tracking changes.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Version History ({{ $versions->count() }})</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Current version: {{ $cv->updated_at->format('M d, Y \a\t g:i A') }}
                </div>
            </div>

            @foreach($versions as $version)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <x-filament::icon icon="heroicon-o-archive-box" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        Version #{{ $loop->iteration }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $version->created_at->format('M d, Y \a\t g:i A') }}
                                        <span class="text-gray-400">•</span>
                                        {{ $version->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            @if($version->reason)
                                <div class="ml-13">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-medium">Reason:</span> {{ $version->reason }}
                                    </p>
                                </div>
                            @endif

                            @php
                                $snapshotData = json_decode($version->snapshot_json, true);
                                $sectionCount = count($snapshotData['sections'] ?? []);
                                $experienceCount = 0;
                                $skillCount = 0;

                                foreach ($snapshotData['sections'] ?? [] as $section) {
                                    if ($section['section_type'] === 'experience') {
                                        $experienceCount += count($section['experiences'] ?? []);
                                    } elseif ($section['section_type'] === 'skills') {
                                        $skillCount += count($section['skill_categories'] ?? []);
                                    }
                                }
                            @endphp

                            <div class="mt-3 flex flex-wrap gap-2 ml-13">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $sectionCount }} sections
                                </span>
                                @if($experienceCount > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                        {{ $experienceCount }} experiences
                                    </span>
                                @endif
                                @if($skillCount > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300">
                                        {{ $skillCount }} skill categories
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            @if($loop->first)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/20 dark:text-success-300">
                                    Latest
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <strong>Note:</strong> Version snapshots are read-only historical records. To restore a previous version, you'll need to manually recreate the CV based on the snapshot data.
            </p>
        </div>
    @endif
</div>
