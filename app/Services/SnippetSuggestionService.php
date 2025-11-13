<?php

namespace App\Services;

use App\Models\ExperienceSnippet;
use Illuminate\Support\Collection;

class SnippetSuggestionService
{
    /**
     * Suggest snippets based on job title and skills
     */
    public function suggestForRole(string $jobTitle, array $skills = []): Collection
    {
        $roleType = $this->inferRoleType($jobTitle);

        $query = ExperienceSnippet::query();

        // Filter by role type if inferred
        if ($roleType) {
            $query->where(function ($q) use ($roleType) {
                $q->where('role_type', $roleType)
                    ->orWhereNull('role_type');
            });
        }

        // Filter by skills/tags
        if (! empty($skills)) {
            $query->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('tags', $skill);
                }
            });
        }

        return $query->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get snippets by category
     */
    public function getByCategory(string $category): Collection
    {
        return ExperienceSnippet::where('category', $category)
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Search snippets
     */
    public function search(string $query): Collection
    {
        return ExperienceSnippet::search($query)
            ->orderBy('usage_count', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get most used snippets
     */
    public function getMostUsed(int $limit = 10): Collection
    {
        return ExperienceSnippet::mostUsed($limit)->get();
    }

    /**
     * Get recently used snippets
     */
    public function getRecentlyUsed(int $limit = 10): Collection
    {
        return ExperienceSnippet::recentlyUsed($limit)->get();
    }

    /**
     * Infer role type from job title
     */
    private function inferRoleType(string $jobTitle): ?string
    {
        $title = strtolower($jobTitle);

        $roleMap = [
            'engineer' => ['engineer', 'developer', 'programmer'],
            'senior_engineer' => ['senior engineer', 'senior developer', 'sr engineer', 'sr developer'],
            'lead_engineer' => ['lead engineer', 'lead developer', 'tech lead', 'technical lead'],
            'architect' => ['architect', 'principal engineer', 'staff engineer'],
            'manager' => ['manager', 'engineering manager', 'team lead'],
            'product_manager' => ['product manager', 'product owner', 'pm'],
            'designer' => ['designer', 'ux', 'ui', 'product designer'],
            'data_scientist' => ['data scientist', 'machine learning', 'ml engineer', 'ai engineer'],
            'devops' => ['devops', 'sre', 'platform engineer', 'infrastructure'],
            'qa' => ['qa', 'test engineer', 'quality assurance', 'sdet'],
        ];

        foreach ($roleMap as $roleType => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($title, $keyword)) {
                    return $roleType;
                }
            }
        }

        return null;
    }
}
