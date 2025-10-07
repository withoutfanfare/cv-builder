<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PDFSnapshot extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'pdf_snapshots';

    protected $fillable = [
        'job_application_id',
        'cv_id',
        'cv_version_id',
        'file_path',
        'hash',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function cvVersion(): BelongsTo
    {
        return $this->belongsTo(CVVersion::class);
    }
}
