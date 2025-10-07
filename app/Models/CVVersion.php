<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CVVersion extends Model
{
    public $timestamps = false;

    protected $table = 'cv_versions';

    protected $fillable = [
        'cv_id',
        'snapshot_json',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'snapshot_json' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CVVersion $cvVersion) {
            if (! $cvVersion->created_at) {
                $cvVersion->created_at = now();
            }
        });
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
