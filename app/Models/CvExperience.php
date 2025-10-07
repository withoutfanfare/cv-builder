<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvExperience extends Model
{
    protected $fillable = [
        'cv_section_id',
        'job_title',
        'company_name',
        'company_url',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'highlights',
        'display_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'highlights' => 'array',
    ];

    public function cvSection(): BelongsTo
    {
        return $this->belongsTo(CvSection::class);
    }
}
