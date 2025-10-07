<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvProject extends Model
{
    protected $fillable = [
        'cv_section_id',
        'project_name',
        'project_url',
        'description',
        'technologies',
        'display_order',
    ];

    public function cvSection(): BelongsTo
    {
        return $this->belongsTo(CvSection::class);
    }
}
