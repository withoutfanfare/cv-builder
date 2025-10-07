<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PdfTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'view_path',
        'preview_image_path',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class);
    }

    public static function default(): self
    {
        return static::where('is_default', true)->firstOrFail();
    }
}
