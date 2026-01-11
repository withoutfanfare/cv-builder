<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\CVVersion;

class CvDiffService
{
    /**
     * Compare two CV versions and generate a diff
     */
    public function diff(Cv $currentCv, CVVersion $previousVersion): array
    {
        $current = $this->flattenCvData($currentCv);
        $previous = json_decode($previousVersion->snapshot_json, true);
        $previousFlat = $this->flattenSnapshotData($previous);

        return [
            'added' => $this->findAdded($current, $previousFlat),
            'removed' => $this->findRemoved($current, $previousFlat),
            'modified' => $this->findModified($current, $previousFlat),
            'unchanged_count' => $this->countUnchanged($current, $previousFlat),
        ];
    }

    /**
     * Flatten CV data for comparison
     */
    private function flattenCvData(Cv $cv): array
    {
        $data = [];

        // Header info
        if ($cv->headerInfo) {
            $data['header'] = [
                'full_name' => $cv->headerInfo->full_name,
                'email' => $cv->headerInfo->email,
                'phone' => $cv->headerInfo->phone,
                'location' => $cv->headerInfo->location,
            ];
        }

        // Experiences
        foreach ($cv->experiences as $exp) {
            $key = "experience_{$exp->job_title}_{$exp->company_name}";
            $data[$key] = [
                'type' => 'experience',
                'job_title' => $exp->job_title,
                'company_name' => $exp->company_name,
                'location' => $exp->location,
                'start_date' => $exp->start_date?->format('Y-m-d'),
                'end_date' => $exp->end_date?->format('Y-m-d'),
                'highlights' => $exp->highlights ?? [],
            ];
        }

        // Skills
        foreach ($cv->skillCategories as $category) {
            $key = "skills_{$category->category_name}";
            $data[$key] = [
                'type' => 'skills',
                'category' => $category->category_name,
                'skills' => $category->skills ?? [],
            ];
        }

        // Projects
        foreach ($cv->projects as $project) {
            $key = "project_{$project->project_name}";
            $data[$key] = [
                'type' => 'project',
                'name' => $project->project_name,
                'description' => $project->description,
                'technologies' => $project->technologies,
            ];
        }

        // Education
        foreach ($cv->education as $edu) {
            $key = "education_{$edu->degree}_{$edu->institution}";
            $data[$key] = [
                'type' => 'education',
                'degree' => $edu->degree,
                'institution' => $edu->institution,
                'start_year' => $edu->start_year,
                'end_year' => $edu->end_year,
            ];
        }

        return $data;
    }

    /**
     * Flatten snapshot JSON data
     */
    private function flattenSnapshotData(array $snapshot): array
    {
        $data = [];

        // This would need to be adapted based on the actual snapshot structure
        // For now, return a simplified version
        return $snapshot;
    }

    /**
     * Find added items
     */
    private function findAdded(array $current, array $previous): array
    {
        $added = [];

        foreach ($current as $key => $value) {
            if (! isset($previous[$key])) {
                $added[$key] = $value;
            }
        }

        return $added;
    }

    /**
     * Find removed items
     */
    private function findRemoved(array $current, array $previous): array
    {
        $removed = [];

        foreach ($previous as $key => $value) {
            if (! isset($current[$key])) {
                $removed[$key] = $value;
            }
        }

        return $removed;
    }

    /**
     * Find modified items
     */
    private function findModified(array $current, array $previous): array
    {
        $modified = [];

        foreach ($current as $key => $value) {
            if (isset($previous[$key]) && $value !== $previous[$key]) {
                $modified[$key] = [
                    'before' => $previous[$key],
                    'after' => $value,
                ];
            }
        }

        return $modified;
    }

    /**
     * Count unchanged items
     */
    private function countUnchanged(array $current, array $previous): int
    {
        $unchanged = 0;

        foreach ($current as $key => $value) {
            if (isset($previous[$key]) && $value === $previous[$key]) {
                $unchanged++;
            }
        }

        return $unchanged;
    }

    /**
     * Get summary of changes
     */
    public function getSummary(array $diff): array
    {
        return [
            'total_changes' => count($diff['added']) + count($diff['removed']) + count($diff['modified']),
            'added_count' => count($diff['added']),
            'removed_count' => count($diff['removed']),
            'modified_count' => count($diff['modified']),
            'unchanged_count' => $diff['unchanged_count'],
        ];
    }
}
