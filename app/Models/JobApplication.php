<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobApplication extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'company_name',
        'company_website',
        'company_notes',
        'point_of_contact_name',
        'point_of_contact_email',
        'send_status',
        'application_status',
        'interview_dates',
        'interview_notes',
        'notes',
        'job_title',
        'source',
        'application_deadline',
        'next_action_date',
        'job_description',
        'last_activity_at',
        'withdrawn_at',
        'ai_review_data',
        'ai_review_requested_at',
        'ai_review_completed_at',
        'ai_review_cost_cents',
    ];

    protected $casts = [
        'interview_dates' => 'array',
        'application_deadline' => 'date',
        'next_action_date' => 'date',
        'last_activity_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'ai_review_data' => 'array',
        'ai_review_requested_at' => 'datetime',
        'ai_review_completed_at' => 'datetime',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function pdfSnapshot(): HasOne
    {
        return $this->hasOne(PDFSnapshot::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ApplicationEvent::class)->orderBy('occurred_at', 'desc');
    }

    /**
     * Check if review is stale (CV modified after review)
     */
    public function isReviewStale(): bool
    {
        if (! $this->ai_review_completed_at) {
            return false;
        }

        return $this->cv->updated_at > $this->ai_review_completed_at;
    }

    /**
     * Get review data as structured object
     */
    public function getReviewData(): ?array
    {
        return $this->ai_review_data;
    }

    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class)->orderBy('version', 'desc');
    }

    public function scopeNeedsAttention(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where(function ($q2) {
                $q2->where('next_action_date', '<=', now())
                    ->orWhere('send_status', 'draft')
                    ->orWhere(function ($q3) {
                        $q3->whereIn('application_status', ['pending', 'interviewing'])
                            ->whereNull('next_action_date');
                    });
            })->whereNotIn('application_status', ['rejected', 'withdrawn']);
        });
    }

    /**
     * Get the latest cover letter version
     */
    public function getLatestCoverLetter(): ?CoverLetter
    {
        return $this->coverLetters()->first();
    }

    /**
     * Get the sent cover letter
     */
    public function getSentCoverLetter(): ?CoverLetter
    {
        return $this->coverLetters()->where('is_sent', true)->first();
    }
}
