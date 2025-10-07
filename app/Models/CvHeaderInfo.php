<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvHeaderInfo extends Model
{
    use HasFactory;

    protected $table = 'cv_header_info';

    protected $fillable = [
        'cv_id',
        'full_name',
        'job_title',
        'phone',
        'email',
        'location',
        'linkedin_url',
        'github_url',
        'website_url',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
