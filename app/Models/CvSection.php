<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CvSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'section_type',
        'title',
        'display_order',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(CvSummary::class);
    }

    public function skillCategories(): HasMany
    {
        return $this->hasMany(CvSkillCategory::class)->orderBy('display_order');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CvExperience::class)->orderBy('display_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(CvProject::class)->orderBy('display_order');
    }

    public function education(): HasMany
    {
        return $this->hasMany(CvEducation::class)->orderBy('display_order');
    }

    public function reference(): HasOne
    {
        return $this->hasOne(CvReference::class);
    }

    public function customSection(): HasOne
    {
        return $this->hasOne(CvCustomSection::class);
    }

    public function skillEvidence(): MorphMany
    {
        return $this->morphMany(SkillEvidence::class, 'evidenceable');
    }
}
