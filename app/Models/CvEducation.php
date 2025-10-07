<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvEducation extends Model
{
    protected $fillable = [
        'cv_section_id',
        'degree',
        'institution',
        'start_year',
        'end_year',
        'description',
        'display_order',
    ];

    public function cvSection(): BelongsTo
    {
        return $this->belongsTo(CvSection::class);
    }
}
