<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoverLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'template',
        'body',
        'tone',
        'version',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coverLetter) {
            // Auto-increment version per job_application_id
            $maxVersion = static::where('job_application_id', $coverLetter->job_application_id)
                ->max('version');
            $coverLetter->version = ($maxVersion ?? 0) + 1;
        });

        static::updating(function ($coverLetter) {
            // Prevent updates to sent cover letters
            if ($coverLetter->getOriginal('is_sent') === true) {
                throw new \Exception('Cannot update a sent cover letter');
            }
        });
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
