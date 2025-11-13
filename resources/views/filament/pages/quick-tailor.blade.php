<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-lg bg-gradient-to-r from-primary-500/10 to-purple-500/10 border border-primary-500/20 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-500 to-purple-500 flex items-center justify-center">
                        <x-filament::icon icon="heroicon-o-bolt" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Quick CV Tailor - From Job Description to Tailored PDF in Minutes
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        This wizard will guide you through selecting a CV, analyzing it against a job description with AI,
                        and automatically applying improvements. You'll have a perfectly tailored CV in just a few clicks!
                    </p>
                </div>
            </div>
        </div>

        {{ $this->form }}

        <div class="flex justify-end gap-3">
            @if($this->analysis === null && !empty($this->data['cv_id'] ?? null) && !empty($this->data['job_description'] ?? null))
                <x-filament::button
                    wire:click="analyzeCV"
                    color="primary"
                    icon="heroicon-o-sparkles"
                    size="lg"
                >
                    Analyze with AI
                </x-filament::button>
            @endif
        </div>
    </div>
</x-filament-panels::page>
