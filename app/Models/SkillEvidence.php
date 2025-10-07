<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SkillEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'skill_name',
        'evidenceable_type',
        'evidenceable_id',
        'notes',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function evidenceable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scope for case-insensitive skill name search
    public function scopeWhereSkillName($query, string $skillName)
    {
        return $query->whereRaw('LOWER(skill_name) = ?', [strtolower($skillName)]);
    }
}
