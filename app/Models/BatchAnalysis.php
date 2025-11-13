<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_id',
        'name',
        'description',
        'job_descriptions',
        'results',
        'total_cost_cents',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'job_descriptions' => 'array',
        'results' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    /**
     * Check if analysis is complete
     */
    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if analysis is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if analysis failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get best match from results
     */
    public function getBestMatch(): ?array
    {
        if (! $this->results || ! $this->isComplete()) {
            return null;
        }

        $bestScore = 0;
        $bestMatch = null;

        foreach ($this->results as $result) {
            $score = $result['match_score'] ?? 0;
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $result;
            }
        }

        return $bestMatch;
    }

    /**
     * Get average match score
     */
    public function getAverageScore(): float
    {
        if (! $this->results || ! $this->isComplete()) {
            return 0;
        }

        $scores = array_map(fn ($result) => $result['match_score'] ?? 0, $this->results);

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
    }

    /**
     * Get sorted results by match score
     */
    public function getSortedResults(): array
    {
        if (! $this->results) {
            return [];
        }

        $results = $this->results;
        usort($results, fn ($a, $b) => ($b['match_score'] ?? 0) <=> ($a['match_score'] ?? 0));

        return $results;
    }
}
