<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorMapping extends Model
{
    use Blameable;

    protected $fillable = [
        'internal_indicator_id',
        'external_indicator_id',
        'is_primary',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function internalIndicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'internal_indicator_id');
    }

    public function externalIndicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'external_indicator_id');
    }
}
