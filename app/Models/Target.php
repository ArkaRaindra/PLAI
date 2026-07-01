<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use Blameable;

    protected $fillable = [
        'indicator_id',
        'quality_period_id',
        'user_position_id',
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
}
