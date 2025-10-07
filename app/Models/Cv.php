<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cv extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'pdf_template_id',
    ];

    public function headerInfo(): HasOne
    {
        return $this->hasOne(CvHeaderInfo::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CvSection::class)->orderBy('display_order');
    }

    public function summary(): HasOne
    {
        return $this->hasOne(CvSummary::class)
            ->whereHas('cvSection', fn ($q) => $q->where('cv_id', $this->id)->where('section_type', 'summary'));
    }

    public function skillCategories(): HasManyThrough
    {
        return $this->hasManyThrough(
            CvSkillCategory::class,
            CvSection::class,
            'cv_id',
            'cv_section_id',
            'id',
            'id'
        )->where('cv_sections.section_type', 'skills')->orderBy('display_order');
    }

    public function experiences(): HasManyThrough
    {
        return $this->hasManyThrough(
            CvExperience::class,
            CvSection::class,
            'cv_id',
            'cv_section_id',
            'id',
            'id'
        )->where('cv_sections.section_type', 'experience')->orderBy('display_order');
    }

    public function projects(): HasManyThrough
    {
        return $this->hasManyThrough(
            CvProject::class,
            CvSection::class,
            'cv_id',
            'cv_section_id',
            'id',
            'id'
        )->where('cv_sections.section_type', 'projects')->orderBy('display_order');
    }

    public function education(): HasManyThrough
    {
        return $this->hasManyThrough(
            CvEducation::class,
            CvSection::class,
            'cv_id',
            'cv_section_id',
            'id',
            'id'
        )->where('cv_sections.section_type', 'education')->orderBy('display_order');
    }

    public function reference(): HasOne
    {
        return $this->hasOne(CvReference::class)
            ->whereHas('cvSection', fn ($q) => $q->where('cv_id', $this->id)->where('section_type', 'references'));
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function customSections(): HasManyThrough
    {
        return $this->hasManyThrough(
            CvCustomSection::class,
            CvSection::class,
            'cv_id',
            'cv_section_id',
            'id',
            'id'
        )->where('cv_sections.section_type', 'custom')->orderBy('display_order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CVVersion::class);
    }

    public function pdfSnapshots(): HasMany
    {
        return $this->hasMany(PDFSnapshot::class);
    }

    public function pdfTemplate(): BelongsTo
    {
        return $this->belongsTo(PdfTemplate::class);
    }

    public function getTemplateAttribute(): PdfTemplate
    {
        return $this->pdfTemplate ?? PdfTemplate::default();
    }

    // Phase 3 relationships
    public function sectionFocusProfiles(): HasMany
    {
        return $this->hasMany(SectionFocusProfile::class);
    }

    public function skillEvidence(): HasMany
    {
        return $this->hasMany(SkillEvidence::class);
    }

    /**
     * Get sections filtered and reordered by profile
     */
    public function getSectionsWithProfile(int $profileId)
    {
        $profile = $this->sectionFocusProfiles()->findOrFail($profileId);

        // Filter sections to only those in included_section_ids
        $filteredSections = $this->sections()
            ->whereIn('id', $profile->included_section_ids)
            ->get();

        // Reorder based on section_order
        $ordered = collect();
        foreach ($profile->section_order as $sectionId) {
            $section = $filteredSections->firstWhere('id', $sectionId);
            if ($section) {
                $ordered->push($section);
            }
        }

        return $ordered;
    }

    /**
     * Get skills aggregated with evidence count
     */
    public function getSkillsWithEvidence(): array
    {
        $evidence = $this->skillEvidence()
            ->with('evidenceable')
            ->get();

        $skillsSummary = [];

        foreach ($evidence as $item) {
            $skillName = $item->skill_name;

            if (! isset($skillsSummary[$skillName])) {
                $skillsSummary[$skillName] = [
                    'evidence_count' => 0,
                    'evidence_types' => [],
                ];
            }

            $skillsSummary[$skillName]['evidence_count']++;

            $type = $item->evidenceable_type;
            if (! in_array($type, $skillsSummary[$skillName]['evidence_types'])) {
                $skillsSummary[$skillName]['evidence_types'][] = $type;
            }
        }

        return $skillsSummary;
    }

    /**
     * Get skills as array for CV review analysis
     */
    public function getSkillsAttribute(): array
    {
        return $this->skillCategories->flatMap(function ($category) {
            return collect($category->skills)->pluck('name')->toArray();
        })->unique()->values()->toArray();
    }

    /**
     * Get experiences as array for CV review analysis
     */
    public function getExperiencesAttribute(): array
    {
        return $this->experiences()->get()->map(function ($exp) {
            return [
                'title' => $exp->job_title,
                'company' => $exp->company_name,
                'highlights' => $exp->highlights ?? [],
            ];
        })->toArray();
    }

    /**
     * Get education as array for CV review analysis
     */
    public function getEducationAttribute(): array
    {
        return $this->education()->get()->map(function ($edu) {
            return [
                'degree' => $edu->degree,
                'institution' => $edu->institution,
            ];
        })->toArray();
    }

    /**
     * Get highlights as array for CV review analysis
     */
    public function getHighlightsAttribute(): array
    {
        // Flatten all highlights from experiences and other sections
        $allHighlights = [];

        foreach ($this->experiences()->get() as $exp) {
            if (! empty($exp->highlights)) {
                $allHighlights = array_merge($allHighlights, $exp->highlights);
            }
        }

        return $allHighlights;
    }

    /**
     * Clone this CV with all its sections and create a version snapshot
     */
    public function cloneCv(string $reason = 'manual clone'): Cv
    {
        return DB::transaction(function () use ($reason) {
            // Step 1: Create version snapshot of current CV
            CVVersion::create([
                'cv_id' => $this->id,
                'snapshot_json' => $this->toArray(),
                'reason' => $reason,
                'created_at' => now(),
            ]);

            // Step 2: Clone the CV
            $clonedCv = $this->replicate();
            $clonedCv->title = $this->title.' (Copy)';
            $clonedCv->save();

            // Step 3: Deep copy header info
            if ($this->headerInfo) {
                $clonedHeaderInfo = $this->headerInfo->replicate();
                $clonedHeaderInfo->cv_id = $clonedCv->id;
                $clonedHeaderInfo->save();
            }

            // Step 4: Deep copy all sections
            foreach ($this->sections as $section) {
                $clonedSection = $section->replicate();
                $clonedSection->cv_id = $clonedCv->id;
                $clonedSection->save();

                // Copy section-specific content based on section_type
                switch ($section->section_type) {
                    case 'summary':
                        if ($section->summary) {
                            $clonedSummary = $section->summary->replicate();
                            $clonedSummary->cv_section_id = $clonedSection->id;
                            $clonedSummary->save();
                        }
                        break;

                    case 'skills':
                        if ($section->skillCategories) {
                            foreach ($section->skillCategories as $skillCategory) {
                                $clonedSkillCategory = $skillCategory->replicate();
                                $clonedSkillCategory->cv_section_id = $clonedSection->id;
                                $clonedSkillCategory->save();
                            }
                        }
                        break;

                    case 'experience':
                        if ($section->experiences) {
                            foreach ($section->experiences as $experience) {
                                $clonedExperience = $experience->replicate();
                                $clonedExperience->cv_section_id = $clonedSection->id;
                                $clonedExperience->save();
                            }
                        }
                        break;

                    case 'projects':
                        if ($section->projects) {
                            foreach ($section->projects as $project) {
                                $clonedProject = $project->replicate();
                                $clonedProject->cv_section_id = $clonedSection->id;
                                $clonedProject->save();
                            }
                        }
                        break;

                    case 'education':
                        if ($section->education) {
                            foreach ($section->education as $edu) {
                                $clonedEdu = $edu->replicate();
                                $clonedEdu->cv_section_id = $clonedSection->id;
                                $clonedEdu->save();
                            }
                        }
                        break;

                    case 'references':
                        if ($section->reference) {
                            $clonedReference = $section->reference->replicate();
                            $clonedReference->cv_section_id = $clonedSection->id;
                            $clonedReference->save();
                        }
                        break;

                    case 'custom':
                        if ($section->customSection) {
                            $clonedCustomSection = $section->customSection->replicate();
                            $clonedCustomSection->cv_section_id = $clonedSection->id;
                            $clonedCustomSection->save();
                        }
                        break;
                }
            }

            return $clonedCv;
        });
    }
}
