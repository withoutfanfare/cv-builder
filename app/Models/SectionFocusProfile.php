<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionFocusProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'name',
        'included_section_ids',
        'section_order',
    ];

    protected $casts = [
        'included_section_ids' => 'array',
        'section_order' => 'array',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
