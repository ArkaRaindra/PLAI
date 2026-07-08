<?php

namespace App\Models;

use App\Blameable;
use App\HasTraceability;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Target extends Model
{
    use Blameable, HasTraceability;

    protected $fillable = [
        'indicator_id',
        'quality_period_id',
        'target_value',
        'created_by',
        'updated_by',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function qualityPeriod(): BelongsTo
    {
        return $this->belongsTo(QualityPeriod::class);
    }

    public function realizations(): HasMany
    {
        return $this->hasMany(Realization::class);
    }
}
