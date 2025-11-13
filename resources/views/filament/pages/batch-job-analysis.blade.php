<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg bg-gradient-to-r from-purple-500/10 to-blue-500/10 border border-purple-500/20 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
                        <x-filament::icon icon="heroicon-o-beaker" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Batch Job Analysis - Compare Against Multiple Positions
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Analyze your CV against up to 10 job descriptions simultaneously. Get insights on which positions
                        you're best suited for and identify common skill gaps across multiple roles.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                {{ $this->form }}

                <div class="mt-4 flex justify-end">
                    @foreach ($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </div>

            <div>
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
