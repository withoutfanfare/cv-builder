<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvSkillCategory extends Model
{
    protected $fillable = [
        'cv_section_id',
        'category_name',
        'skills',
        'display_order',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function cvSection(): BelongsTo
    {
        return $this->belongsTo(CvSection::class);
    }
}
