<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_type',
        'value',
        'time_period_start',
        'time_period_end',
        'last_refreshed_at',
    ];

    protected $casts = [
        'time_period_start' => 'date',
        'time_period_end' => 'date',
        'last_refreshed_at' => 'datetime',
        'value' => 'float',
    ];
}
