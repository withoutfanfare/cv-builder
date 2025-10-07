<div style="height: 100%; display: flex; flex-direction: column;">
    @if($reviewData)
        <!-- v2.0 -->
        <div style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">
            {{-- Header --}}
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #374151; background: linear-gradient(to right, rgba(99, 102, 241, 0.1), rgba(79, 70, 229, 0.1));">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <h3 style="font-weight: 600; font-size: 0.875rem; color: #f3f4f6;">CV Review</h3>
                        @php
                            $score = $this->matchScore;
                            $scoreColor = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                        @endphp
                        <x-filament::badge :color="$scoreColor" size="lg">
                            {{ $score }}%
                        </x-filament::badge>
                    </div>
                    <p style="font-size: 0.75rem; color: #9ca3af;">
                        For: {{ $jobApplication->company_name }} - {{ $jobApplication->job_title }}
                    </p>
                </div>

                {{-- Tab Navigation --}}
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #374151; background-color: #1f2937;">
                    @php
                        $tabs = [
                            'skills' => ['label' => 'Skills', 'count' => count($this->skillGaps), 'icon' => '🎯'],
                            'keywords' => ['label' => 'Keywords', 'count' => count($this->missingKeywords), 'icon' => '🔑'],
                            'bullets' => ['label' => 'Bullets', 'count' => count($this->bulletImprovements), 'icon' => '💡'],
                            'language' => ['label' => 'Language', 'count' => count($this->languageSuggestions), 'icon' => '✍️'],
                            'sections' => ['label' => 'Sections', 'count' => count($this->sectionRecommendations), 'icon' => '📋'],
                            'actions' => ['label' => 'Actions', 'count' => count($this->actionChecklist), 'icon' => '✅'],
                        ];
                        $currentTab = $tabs[$activeTab] ?? $tabs['skills'];
                    @endphp
                    <div style="position: relative;">
                        <select 
                            wire:change="setActiveTab($event.target.value)"
                            style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 1rem; background-color: #374151; color: #f3f4f6; border: 1px solid #4b5563; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; appearance: none;"
                        >
                            @foreach($tabs as $tabId => $tab)
                                <option value="{{ $tabId }}" {{ $activeTab === $tabId ? 'selected' : '' }}>
                                    {{ $tab['icon'] }} {{ $tab['label'] }} ({{ $tab['count'] }})
                                </option>
                            @endforeach
                        </select>
                        <div style="pointer-events: none; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;">▼</div>
                    </div>
                </div>

                {{-- Content Area --}}
                <div style="flex: 1; overflow-y: auto; padding: 1.5rem;">
                    {{-- Skills Tab --}}
                    @if($activeTab === 'skills')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-exclamation-circle" style="width: 1.25rem; height: 1.25rem; color: #f87171;" />
                                <span>Missing Skills ({{ count($this->skillGaps) }})</span>
                            </h4>
                            @forelse($this->skillGaps as $index => $gap)
                                <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        <h5 style="font-weight: 600; font-size: 1rem; color: #f3f4f6;">{{ $gap['skill'] ?? 'Unknown' }}</h5>
                                        @if(isset($gap['priority']))
                                            <x-filament::badge :color="($gap['priority'] ?? 'medium') === 'high' ? 'danger' : 'warning'" size="sm">
                                                {{ ucfirst($gap['priority']) }}
                                            </x-filament::badge>
                                        @endif
                                    </div>

                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                        <button
                                            wire:click="addSkillToCv('{{ $gap['skill'] ?? '' }}')"
                                            style="flex: 1; padding: 0.625rem 1rem; background-color: #4f46e5; color: white; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border: none; cursor: pointer;"
                                            onmouseover="this.style.backgroundColor='#4338ca'"
                                            onmouseout="this.style.backgroundColor='#4f46e5'"
                                        >
                                            <x-filament::icon icon="heroicon-o-plus-circle" style="width: 1rem; height: 1rem;" />
                                            <span>Add to CV</span>
                                        </button>
                                        <button
                                            onclick="navigator.clipboard.writeText('{{ $gap['skill'] ?? '' }}'); window.$wireui.notify({title: 'Copied!', description: 'Skill name copied to clipboard', icon: 'success'})"
                                            style="padding: 0.625rem 1rem; background-color: #374151; color: #d1d5db; border: 1px solid #4b5563; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;"
                                            onmouseover="this.style.backgroundColor='#4b5563'"
                                            onmouseout="this.style.backgroundColor='#374151'"
                                        >
                                            <x-filament::icon icon="heroicon-o-clipboard" style="width: 1rem; height: 1rem;" />
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No skill gaps identified! 🎉</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- Keywords Tab --}}
                    @if($activeTab === 'keywords')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-key" style="width: 1.25rem; height: 1.25rem; color: #fbbf24;" />
                                <span>Missing Keywords ({{ count($this->missingKeywords) }})</span>
                            </h4>
                            @forelse($this->missingKeywords as $keyword)
                                <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #f3f4f6;">{{ $keyword }}</span>
                                        <button
                                            wire:click="addSkillToCv('{{ $keyword }}')"
                                            style="padding: 0.375rem 0.75rem; background-color: #4f46e5; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer; white-space: nowrap;"
                                            onmouseover="this.style.backgroundColor='#4338ca'"
                                            onmouseout="this.style.backgroundColor='#4f46e5'"
                                        >
                                            <x-filament::icon icon="heroicon-o-plus-circle" style="width: 1rem; height: 1rem; display: inline;" />
                                        </button>
                                    </div>
                                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">Add this keyword to improve job description match</p>
                                </div>
                            @empty
                                <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">All keywords covered! 🎉</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- Bullets Tab --}}
                    @if($activeTab === 'bullets')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-light-bulb" style="width: 1.25rem; height: 1.25rem; color: #fbbf24;" />
                                <span>Bullet Improvements ({{ count($this->bulletImprovements) }})</span>
                            </h4>
                            @forelse($this->bulletImprovements as $index => $improvement)
                                @php
                                    $currentText = $improvement['original'] ?? null;
                                    $suggestedText = $improvement['improvement'] ?? null;
                                @endphp
                                @if($currentText && $suggestedText)
                                    <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;" x-data="{ showCurrent: true }">
                                        {{-- Toggle Buttons --}}
                                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                                            <button
                                                @click="showCurrent = true"
                                                :style="showCurrent ? 'flex: 1; padding: 0.375rem 0.75rem; background-color: #4b5563; color: #f3f4f6; border: 1px solid #6b7280; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;' : 'flex: 1; padding: 0.375rem 0.75rem; background-color: transparent; color: #9ca3af; border: 1px solid #4b5563; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;'"
                                            >
                                                Current
                                            </button>
                                            <button
                                                @click="showCurrent = false"
                                                :style="!showCurrent ? 'flex: 1; padding: 0.375rem 0.75rem; background-color: #4f46e5; color: white; border: 1px solid #6366f1; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;' : 'flex: 1; padding: 0.375rem 0.75rem; background-color: transparent; color: #9ca3af; border: 1px solid #4b5563; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;'"
                                            >
                                                💡 Suggested
                                            </button>
                                        </div>

                                        {{-- Current Text --}}
                                        <div x-show="showCurrent" style="margin-bottom: 0.75rem;">
                                            <p style="font-size: 0.875rem; color: #d1d5db; background-color: rgba(17, 24, 39, 0.5); padding: 0.75rem; border-radius: 0.5rem;">{{ $currentText }}</p>
                                        </div>

                                        {{-- Suggested Text --}}
                                        <div x-show="!showCurrent" style="margin-bottom: 0.75rem;">
                                            <p style="font-size: 0.875rem; color: #f3f4f6; background-color: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); padding: 0.75rem; border-radius: 0.5rem;">{{ $suggestedText }}</p>
                                        </div>

                                        <button
                                            onclick="navigator.clipboard.writeText('{{ addslashes($suggestedText) }}'); window.$wireui.notify({title: 'Copied!', description: 'Improvement copied to clipboard', icon: 'success'})"
                                            style="width: 100%; padding: 0.625rem 1rem; background-color: #374151; color: #d1d5db; border: 1px solid #4b5563; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;"
                                            onmouseover="this.style.backgroundColor='#4b5563'"
                                            onmouseout="this.style.backgroundColor='#374151'"
                                        >
                                            <x-filament::icon icon="heroicon-o-clipboard-document" style="width: 1rem; height: 1rem;" />
                                            <span>Copy Improvement</span>
                                        </button>
                                    </div>
                                @endif
                            @empty
                                <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Bullets look great! 🎉</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- Language Tab --}}
                    @if($activeTab === 'language')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-chat-bubble-left-right" style="width: 1.25rem; height: 1.25rem; color: #34d399;" />
                                <span>Language Suggestions ({{ count($this->languageSuggestions) }})</span>
                            </h4>
                            @forelse($this->languageSuggestions as $index => $suggestion)
                                @php
                                    $originalText = $suggestion['original'] ?? null;
                                    $improvedText = $suggestion['improvement'] ?? $suggestion['suggestion'] ?? null;
                                    $hasToggle = $originalText && $improvedText;
                                @endphp

                                @if($improvedText)
                                    <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;" @if($hasToggle) x-data="{ showCurrent: true }" @endif>
                                        @if(isset($suggestion['priority']))
                                            @php
                                                $priorityColors = [
                                                    'high' => 'background-color: rgba(239, 68, 68, 0.1); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3);',
                                                    'medium' => 'background-color: rgba(251, 191, 36, 0.1); color: #fcd34d; border-color: rgba(251, 191, 36, 0.3);',
                                                    'low' => 'background-color: rgba(156, 163, 175, 0.1); color: #d1d5db; border-color: rgba(156, 163, 175, 0.3);',
                                                ];
                                                $priorityStyle = $priorityColors[$suggestion['priority']] ?? $priorityColors['low'];
                                            @endphp
                                            <div style="display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.375rem; border: 1px solid; font-size: 0.75rem; font-weight: 500; margin-bottom: 0.75rem; {{ $priorityStyle }}">
                                                {{ ucfirst($suggestion['priority']) }} Priority
                                            </div>
                                        @endif

                                        @if($hasToggle)
                                            {{-- Toggle Buttons --}}
                                            <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                                                <button
                                                    @click="showCurrent = true"
                                                    :style="showCurrent ? 'flex: 1; padding: 0.375rem 0.75rem; background-color: #4b5563; color: #f3f4f6; border: 1px solid #6b7280; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;' : 'flex: 1; padding: 0.375rem 0.75rem; background-color: transparent; color: #9ca3af; border: 1px solid #4b5563; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;'"
                                                >
                                                    Current
                                                </button>
                                                <button
                                                    @click="showCurrent = false"
                                                    :style="!showCurrent ? 'flex: 1; padding: 0.375rem 0.75rem; background-color: #10b981; color: white; border: 1px solid #34d399; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;' : 'flex: 1; padding: 0.375rem 0.75rem; background-color: transparent; color: #9ca3af; border: 1px solid #4b5563; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer;'"
                                                >
                                                    💡 Suggested
                                                </button>
                                            </div>

                                            {{-- Current Text --}}
                                            <div x-show="showCurrent" style="margin-bottom: 0.75rem;">
                                                <p style="font-size: 0.875rem; color: #d1d5db; background-color: rgba(17, 24, 39, 0.5); padding: 0.75rem; border-radius: 0.5rem;">{{ $originalText }}</p>
                                            </div>

                                            {{-- Suggested Text --}}
                                            <div x-show="!showCurrent" style="margin-bottom: 0.75rem;">
                                                <p style="font-size: 0.875rem; color: #f3f4f6; background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.75rem; border-radius: 0.5rem;">{{ $improvedText }}</p>
                                            </div>
                                        @else
                                            {{-- General advice (fallback for old format) --}}
                                            <p style="font-size: 0.875rem; color: #f3f4f6; line-height: 1.5;">{{ $improvedText }}</p>
                                        @endif

                                        @if(isset($suggestion['reason']))
                                            <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem; font-style: italic;">{{ $suggestion['reason'] }}</p>
                                        @endif

                                        @if($hasToggle)
                                            <button
                                                onclick="navigator.clipboard.writeText('{{ addslashes($improvedText) }}'); window.$wireui.notify({title: 'Copied!', description: 'Improvement copied to clipboard', icon: 'success'})"
                                                style="width: 100%; padding: 0.625rem 1rem; background-color: #374151; color: #d1d5db; border: 1px solid #4b5563; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer; margin-top: 0.75rem;"
                                                onmouseover="this.style.backgroundColor='#4b5563'"
                                                onmouseout="this.style.backgroundColor='#374151'"
                                            >
                                                <x-filament::icon icon="heroicon-o-clipboard" style="width: 1rem; height: 1rem;" />
                                                <span>Copy Improvement</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            @empty
                                <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Language is well-aligned! 🎉</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- Sections Tab --}}
                    @if($activeTab === 'sections')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-queue-list" style="width: 1.25rem; height: 1.25rem; color: #fb923c;" />
                                <span>Section Recommendations ({{ count($this->sectionRecommendations) }})</span>
                            </h4>
                            @forelse($this->sectionRecommendations as $index => $rec)
                                <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.5rem;">
                                        <h5 style="font-weight: 600; font-size: 1rem; color: #f3f4f6; text-transform: capitalize;">{{ $rec['section'] ?? 'Section' }}</h5>
                                        @if(isset($rec['priority']))
                                            <x-filament::badge :color="($rec['priority'] ?? 'medium') === 'high' ? 'warning' : 'gray'" size="sm">
                                                {{ ucfirst($rec['priority']) }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                    <p style="font-size: 0.875rem; color: #d1d5db;">{{ $rec['recommendation'] ?? 'No details' }}</p>
                                </div>
                            @empty
                                <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Section order looks good! 🎉</p>
                            @endforelse
                        </div>
                    @endif

                    {{-- Actions Tab --}}
                    @if($activeTab === 'actions')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #f3f4f6; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" style="width: 1.25rem; height: 1.25rem; color: #818cf8;" />
                                <span>Action Checklist ({{ count($this->actionChecklist) }})</span>
                            </h4>
                            @forelse($this->actionChecklist as $index => $action)
                                @php
                                    $actionText = is_array($action) ? ($action['action'] ?? $action['description'] ?? '') : $action;
                                    $priority = is_array($action) ? ($action['priority'] ?? 'medium') : 'medium';
                                @endphp
                                <div style="border-radius: 0.5rem; border: 1px solid #374151; background-color: rgba(31, 41, 55, 0.5); padding: 1rem;">
                                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                        <div style="flex-shrink: 0; width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: rgba(99, 102, 241, 0.2); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700;">
                                            {{ $loop->iteration }}
                                        </div>
                                        <p style="flex: 1; font-size: 0.875rem; color: #d1d5db; padding-top: 0.125rem;">{{ $actionText }}</p>
                                    </div>
                                </div>
                            @empty
                                <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">No actions needed! 🎉</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
    @endif

    {{-- Notification Script --}}
    @script
    <script>
        // Simple notification system
        window.$wireui = window.$wireui || {};
        window.$wireui.notify = function(config) {
            const notification = document.createElement('div');
            // Use Filament's notification system instead
            const notifColor = config.type === 'error' ? '#ef4444' : config.type === 'warning' ? '#f59e0b' : '#10b981';
            notification.style.cssText = `position: fixed !important; top: 80px !important; left: 50% !important; transform: translateX(-50%) !important; background-color: ${notifColor} !important; color: white !important; padding: 1rem 1.5rem !important; border-radius: 0.5rem !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3) !important; z-index: 99999 !important; max-width: 400px !important;`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">${config.title}</p>
                        ${config.description ? `<p class="text-sm">${config.description}</p>` : ''}
                    </div>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        };
    </script>
    @endscript
</div>
