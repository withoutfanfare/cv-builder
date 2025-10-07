<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvReference extends Model
{
    protected $fillable = [
        'cv_section_id',
        'content',
    ];

    public function cvSection(): BelongsTo
    {
        return $this->belongsTo(CvSection::class);
    }
}
