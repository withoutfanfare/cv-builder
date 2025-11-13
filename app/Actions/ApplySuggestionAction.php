<?php

namespace App\Actions;

use App\Models\Cv;
use App\Models\CvExperience;
use App\Models\CvProject;
use App\Models\CvSection;
use Illuminate\Support\Facades\Log;

class ApplySuggestionAction
{
    /**
     * Apply a single language suggestion to the CV
     */
    public function applyLanguageSuggestion(Cv $cv, array $suggestion): bool
    {
        $original = $suggestion['original'] ?? null;
        $improvement = $suggestion['improvement'] ?? null;

        if (! $original || ! $improvement) {
            return false;
        }

        $applied = false;

        // Search and replace in experience highlights
        foreach ($cv->experiences as $experience) {
            if ($this->replaceInExperience($experience, $original, $improvement)) {
                $applied = true;
            }
        }

        // Search and replace in project descriptions
        foreach ($cv->projects as $project) {
            if ($this->replaceInProject($project, $original, $improvement)) {
                $applied = true;
            }
        }

        // Search and replace in summary
        $summarySection = $cv->sections()->where('section_type', 'summary')->first();
        if ($summarySection && $summarySection->summary) {
            if ($this->replaceInSummary($summarySection->summary, $original, $improvement)) {
                $applied = true;
            }
        }

        if ($applied) {
            Log::info('Applied suggestion to CV', [
                'cv_id' => $cv->id,
                'original' => $original,
                'improvement' => $improvement,
            ]);
        }

        return $applied;
    }

    /**
     * Apply all language suggestions from AI review
     */
    public function applyAllLanguageSuggestions(Cv $cv, array $aiReviewData): array
    {
        $applied = [];
        $suggestions = $aiReviewData['language_suggestions'] ?? [];

        foreach ($suggestions as $suggestion) {
            if ($this->applyLanguageSuggestion($cv, $suggestion)) {
                $applied[] = $suggestion;
            }
        }

        // Refresh the CV to ensure all relationships are up to date
        $cv->refresh();

        return $applied;
    }

    /**
     * Apply bullet point improvements
     */
    public function applyBulletImprovement(Cv $cv, array $improvement): bool
    {
        $original = $improvement['original'] ?? null;
        $improved = $improvement['improved'] ?? null;

        if (! $original || ! $improved) {
            return false;
        }

        foreach ($cv->experiences as $experience) {
            if ($this->replaceInExperience($experience, $original, $improved)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace text in experience highlights
     */
    private function replaceInExperience(CvExperience $experience, string $original, string $replacement): bool
    {
        $highlights = $experience->highlights;
        $modified = false;

        if (! is_array($highlights)) {
            return false;
        }

        foreach ($highlights as $index => $highlight) {
            // Case-insensitive search
            if (stripos($highlight, $original) !== false) {
                $highlights[$index] = str_ireplace($original, $replacement, $highlight);
                $modified = true;
            }
        }

        if ($modified) {
            $experience->highlights = $highlights;
            $experience->save();
        }

        return $modified;
    }

    /**
     * Replace text in project description
     */
    private function replaceInProject(CvProject $project, string $original, string $replacement): bool
    {
        if (stripos($project->description, $original) !== false) {
            $project->description = str_ireplace($original, $replacement, $project->description);
            $project->save();

            return true;
        }

        return false;
    }

    /**
     * Replace text in summary
     */
    private function replaceInSummary($summary, string $original, string $replacement): bool
    {
        if (! $summary) {
            return false;
        }

        if (stripos($summary->content, $original) !== false) {
            $summary->content = str_ireplace($original, $replacement, $summary->content);
            $summary->save();

            return true;
        }

        return false;
    }

    /**
     * Add missing skills identified in skill gaps
     */
    public function addMissingSkills(Cv $cv, array $skillGaps): int
    {
        $added = 0;

        // Get or create a "Recommended Skills" category
        $skillSection = $cv->sections()
            ->where('section_type', 'skills')
            ->first();

        if (! $skillSection) {
            // Create skills section if it doesn't exist
            $skillSection = $cv->sections()->create([
                'section_type' => 'skills',
                'title' => 'Skills',
                'display_order' => 1,
            ]);
        }

        $recommendedCategory = $skillSection->skillCategories()
            ->where('category_name', 'Recommended Skills')
            ->first();

        if (! $recommendedCategory) {
            // Find the highest display order
            $maxOrder = $skillSection->skillCategories()->max('display_order') ?? 0;

            $recommendedCategory = $skillSection->skillCategories()->create([
                'category_name' => 'Recommended Skills',
                'skills' => [],
                'display_order' => $maxOrder + 1,
            ]);
        }

        $currentSkills = $recommendedCategory->skills ?? [];

        foreach ($skillGaps as $gap) {
            $skill = $gap['skill'] ?? null;
            if ($skill && ! in_array($skill, $currentSkills)) {
                $currentSkills[] = $skill;
                $added++;
            }
        }

        if ($added > 0) {
            $recommendedCategory->skills = $currentSkills;
            $recommendedCategory->save();
        }

        return $added;
    }
}
