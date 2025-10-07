@php
    $record = $getRecord();
    $reviewData = $record?->ai_review_data;
    $isProcessing = $record?->ai_review_requested_at && !$record?->ai_review_completed_at;
    $isCompleted = $record?->ai_review_completed_at;
@endphp

@if($isProcessing)
    <div class="rounded-lg bg-warning-50 p-6 dark:bg-warning-900/20">
        <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
            <svg class="h-6 w-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div>
                <p class="font-semibold">⏳ Review in progress...</p>
                <p class="text-sm mt-1">Your CV review is being processed. This usually takes 5-10 seconds. Refresh the page to see results.</p>
            </div>
        </div>
    </div>
@endif

@if($isCompleted)
    @php
        $matchScore = $reviewData['match_score'] ?? 0;

        // Filter skill gaps - remove skills already in CV
        $rawSkillGaps = $reviewData['skill_gaps'] ?? [];
        if ($record->cv) {
            $skillsSection = $record->cv->sections->firstWhere('section_type', 'skills');
            if ($skillsSection && $skillsSection->skillCategories) {
                $existingSkills = [];
                foreach ($skillsSection->skillCategories as $category) {
                    $skills = $category->skills ?? [];
                    $existingSkills = array_merge($existingSkills, array_map('strtolower', $skills));
                }
                $rawSkillGaps = array_filter($rawSkillGaps, function($gap) use ($existingSkills) {
                    $skillName = strtolower($gap['skill'] ?? '');
                    return !in_array($skillName, $existingSkills);
                });
            }
        }

        $skillGaps = count($rawSkillGaps);
        $bulletImprovements = count($reviewData['bullet_improvements'] ?? []);
        $languageSuggestions = count($reviewData['language_suggestions'] ?? []);
        $sectionRecs = count($reviewData['section_recommendations'] ?? []);
        $actionItems = count($reviewData['action_checklist'] ?? []);
        $totalSuggestions = $skillGaps + $bulletImprovements + $languageSuggestions + $sectionRecs;

        $scoreText = $matchScore >= 70 ? 'Great Match!' : ($matchScore >= 50 ? 'Good Match' : 'Needs Work');

        // Color styles based on score
        if ($matchScore >= 70) {
            $borderColor = '#10b981'; // green
            $bgColor = 'rgba(16, 185, 129, 0.05)';
            $scoreColor = '#10b981';
        } elseif ($matchScore >= 50) {
            $borderColor = '#f59e0b'; // amber
            $bgColor = 'rgba(245, 158, 11, 0.05)';
            $scoreColor = '#f59e0b';
        } else {
            $borderColor = '#ef4444'; // red
            $bgColor = 'rgba(239, 68, 68, 0.05)';
            $scoreColor = '#ef4444';
        }
    @endphp

    {{-- Stale Review Warning --}}
    @if($record->isReviewStale())
        <div class="rounded-lg bg-warning-50 p-4 mb-4 dark:bg-warning-900/20">
            <div class="flex items-center gap-2 text-warning-700 dark:text-warning-300">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5" />
                <span class="font-medium">Review Out of Date</span>
            </div>
            <p class="mt-1 text-sm text-warning-600 dark:text-warning-400">
                Your CV has been modified since this review. Consider regenerating for up-to-date recommendations.
            </p>
        </div>
    @endif

    <div style="border-radius: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.1); background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
        {{-- Match Score Header --}}
        <div class="flex items-center justify-between" style="margin-bottom: 2.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center;">
                        <x-filament::icon icon="heroicon-o-sparkles" style="width: 1.25rem; height: 1.25rem; color: white;" />
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: #f3f4f6;">AI Review Complete</h3>
                </div>
                <p style="font-size: 0.875rem; color: #9ca3af; margin-left: 3.25rem;">
                    Analysis cost: ${{ number_format(($record->ai_review_cost_cents ?? 0) / 100, 2) }}
                </p>
            </div>
            <div class="text-right">
                <div style="font-size: 3rem; font-weight: 700; line-height: 1; color: {{ $scoreColor }}; text-shadow: 0 0 20px {{ $scoreColor }}40;">{{ $matchScore }}%</div>
                <div style="font-size: 0.875rem; font-weight: 600; color: {{ $scoreColor }}; margin-top: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">{{ $scoreText }}</div>
            </div>
        </div>

        {{-- Quick Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3" style="margin-bottom: 2rem;">
            <div style="border-radius: 0.625rem; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid rgba(239, 68, 68, 0.2); padding: 1.25rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: radial-gradient(circle, rgba(239, 68, 68, 0.2) 0%, transparent 70%);"></div>
                <div style="font-size: 2rem; font-weight: 700; color: #fca5a5; line-height: 1;">{{ $skillGaps }}</div>
                <div style="font-size: 0.8125rem; color: #d1d5db; margin-top: 0.5rem; font-weight: 500;">Skill Gaps</div>
            </div>
            <div style="border-radius: 0.625rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%); border: 1px solid rgba(99, 102, 241, 0.2); padding: 1.25rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%);"></div>
                <div style="font-size: 2rem; font-weight: 700; color: #a5b4fc; line-height: 1;">{{ $bulletImprovements }}</div>
                <div style="font-size: 0.8125rem; color: #d1d5db; margin-top: 0.5rem; font-weight: 500;">Bullet Tips</div>
            </div>
            <div style="border-radius: 0.625rem; background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(251, 191, 36, 0.05) 100%); border: 1px solid rgba(251, 191, 36, 0.2); padding: 1.25rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: radial-gradient(circle, rgba(251, 191, 36, 0.2) 0%, transparent 70%);"></div>
                <div style="font-size: 2rem; font-weight: 700; color: #fcd34d; line-height: 1;">{{ $sectionRecs }}</div>
                <div style="font-size: 0.8125rem; color: #d1d5db; margin-top: 0.5rem; font-weight: 500;">Section Tips</div>
            </div>
            <div style="border-radius: 0.625rem; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.25rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);"></div>
                <div style="font-size: 2rem; font-weight: 700; color: #6ee7b7; line-height: 1;">{{ $actionItems }}</div>
                <div style="font-size: 0.8125rem; color: #d1d5db; margin-top: 0.5rem; font-weight: 500;">Action Items</div>
            </div>
        </div>

        {{-- Call to Action --}}
        <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid rgba(99, 102, 241, 0.3); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);"></div>
            <div class="flex items-start gap-4" style="position: relative;">
                <div class="flex-shrink-0">
                    <div style="width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);">
                        <x-filament::icon icon="heroicon-o-arrow-right" style="width: 1.5rem; height: 1.5rem; color: white;" />
                    </div>
                </div>
                <div class="flex-1">
                    <h4 style="font-weight: 600; font-size: 1.0625rem; color: #f3f4f6; margin-bottom: 0.5rem;">Ready to Improve Your CV?</h4>
                    <p style="font-size: 0.875rem; color: #d1d5db; line-height: 1.5; margin-bottom: 1.25rem;">
                        View detailed suggestions and apply improvements directly to your CV with our interactive review sidebar.
                    </p>
                    <a
                        href="{{ route('filament.admin.resources.cvs.edit', ['record' => $record->cv_id, 'review' => $record->id]) }}"
                        style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.625rem 1.5rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; font-weight: 600; font-size: 0.875rem; border-radius: 0.5rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 8px -1px rgba(99, 102, 241, 0.5)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(99, 102, 241, 0.4)'"
                    >
                        <x-filament::icon icon="heroicon-o-pencil-square" style="width: 1.125rem; height: 1.125rem;" />
                        Open Review Sidebar
                    </a>
                </div>
            </div>
        </div>

        {{-- High Priority Quick Glance --}}
        @if($skillGaps > 0)
            <div style="margin-top: 1.5rem; border-radius: 0.75rem; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid rgba(239, 68, 68, 0.25); padding: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1rem;">
                    <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background-color: rgba(239, 68, 68, 0.2); display: flex; align-items: center; justify-content: center;">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 1.125rem; height: 1.125rem; color: #fca5a5;" />
                    </div>
                    <h5 style="font-weight: 600; font-size: 0.9375rem; color: #fca5a5;">High Priority Skill Gaps</h5>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($rawSkillGaps as $gap)
                        @if(($gap['priority'] ?? 'medium') === 'high')
                            <span style="display: inline-flex; align-items: center; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 600; background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3);">
                                {{ $gap['skill'] ?? 'Unknown' }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
