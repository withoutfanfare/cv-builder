<?php

namespace App\Livewire;

use App\Models\JobApplication;
use Filament\Notifications\Notification;
use Livewire\Component;

class CvReviewSidebar extends Component
{
    public ?int $reviewId = null;

    public ?array $reviewData = null;

    public ?JobApplication $jobApplication = null;

    public bool $isCollapsed = false; // Start visible

    public string $activeTab = 'skills';

    public function mount(?int $reviewId = null)
    {
        $this->reviewId = $reviewId;

        if ($this->reviewId) {
            $this->jobApplication = JobApplication::with([
                'cv.sections.skillCategories',
            ])->find($this->reviewId);

            if ($this->jobApplication?->ai_review_data) {
                $this->reviewData = $this->jobApplication->ai_review_data;
            }
        }
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = ! $this->isCollapsed;
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function getSkillGapsProperty()
    {
        $gaps = $this->reviewData['skill_gaps'] ?? [];

        // Filter out skills that already exist in the CV
        if ($this->jobApplication?->cv) {
            $cv = $this->jobApplication->cv;
            $skillsSection = $cv->sections->firstWhere('section_type', 'skills');

            if ($skillsSection && $skillsSection->skillCategories) {
                $existingSkills = [];
                foreach ($skillsSection->skillCategories as $category) {
                    $skills = $category->skills ?? [];
                    $existingSkills = array_merge($existingSkills, array_map('strtolower', $skills));
                }

                // Filter gaps to only show skills not in CV
                $gaps = array_filter($gaps, function ($gap) use ($existingSkills) {
                    $skillName = strtolower($gap['skill'] ?? '');

                    return ! in_array($skillName, $existingSkills);
                });
            }
        }

        return array_values($gaps); // Re-index array
    }

    public function getBulletImprovementsProperty()
    {
        return $this->reviewData['bullet_improvements'] ?? [];
    }

    public function getLanguageSuggestionsProperty()
    {
        return $this->reviewData['language_suggestions'] ?? [];
    }

    public function getSectionRecommendationsProperty()
    {
        return $this->reviewData['section_recommendations'] ?? [];
    }

    public function getActionChecklistProperty()
    {
        $actions = $this->reviewData['action_checklist'] ?? [];

        // Filter out skill-related actions that are already in the CV
        if ($this->jobApplication?->cv) {
            $cv = $this->jobApplication->cv;
            $skillsSection = $cv->sections->firstWhere('section_type', 'skills');

            if ($skillsSection && $skillsSection->skillCategories) {
                $existingSkills = [];
                foreach ($skillsSection->skillCategories as $category) {
                    $skills = $category->skills ?? [];
                    $existingSkills = array_merge($existingSkills, array_map('strtolower', $skills));
                }

                // Filter actions that mention skills already in CV
                $actions = array_filter($actions, function ($action) use ($existingSkills) {
                    $actionText = is_array($action) ? ($action['action'] ?? $action['description'] ?? '') : $action;
                    $actionLower = strtolower($actionText);

                    // Check if any existing skill is mentioned in this action
                    foreach ($existingSkills as $skill) {
                        if (stripos($actionText, $skill) !== false) {
                            return false; // Filter out if skill is mentioned
                        }
                    }

                    return true;
                });
            }
        }

        return array_values($actions); // Re-index array
    }

    public function getMatchScoreProperty()
    {
        return $this->reviewData['match_score'] ?? 0;
    }

    public function getMissingKeywordsProperty()
    {
        if (! $this->jobApplication?->cv || ! $this->jobApplication->job_description) {
            return [];
        }

        // Get CV content
        $cv = $this->jobApplication->cv;
        $cvContent = $cv->title.' ';
        foreach ($cv->sections as $section) {
            $cvContent .= $section->title.' ';
        }

        // Calculate keyword coverage
        $service = app(\App\Services\KeywordCoverageService::class);
        $coverage = $service->calculateCoverage($this->jobApplication->job_description, $cvContent);

        // Return top 10 missing keywords
        return array_slice($coverage['missing_keywords'] ?? [], 0, 10);
    }

    public function addSkillToCv(string $skillName)
    {
        if (! $this->jobApplication?->cv) {
            Notification::make()
                ->danger()
                ->title('CV not found')
                ->send();

            return;
        }

        $cv = $this->jobApplication->cv;

        // Check if skill already exists in ANY category (case-insensitive)
        // Use the loaded relationship, not a new query
        $skillsSection = $cv->sections->firstWhere('section_type', 'skills');

        if ($skillsSection && $skillsSection->skillCategories) {
            foreach ($skillsSection->skillCategories as $category) {
                $existingSkills = $category->skills ?? [];
                $skillsLower = array_map('strtolower', $existingSkills);

                if (in_array(strtolower($skillName), $skillsLower)) {
                    Notification::make()
                        ->warning()
                        ->title('Skill Already in CV')
                        ->body("'{$skillName}' already exists in '{$category->category_name}' category")
                        ->send();

                    return;
                }
            }
        }

        // If no skills section, create one
        if (! $skillsSection) {
            $skillsSection = $cv->sections()->create([
                'section_type' => 'skills',
                'display_order' => 0,
            ]);
        }

        // Add to Technical Skills category
        $skillCategory = $skillsSection->skillCategories()->firstOrCreate(
            ['category_name' => 'Technical Skills'],
            ['skills' => [], 'display_order' => 0]
        );

        $skills = $skillCategory->skills ?? [];
        $skills[] = $skillName;
        $skillCategory->update(['skills' => $skills]);

        Notification::make()
            ->success()
            ->title('Skill Added')
            ->body("'{$skillName}' added to Technical Skills")
            ->send();

        // Reload the CV relationship to get fresh data
        $this->jobApplication->load('cv.sections.skillCategories');
    }

    public function render()
    {
        return view('livewire.cv-review-sidebar');
    }
}
