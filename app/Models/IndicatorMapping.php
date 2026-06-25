<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorMapping extends Model
{
    protected $fillable = [
        'internal_indicator_id', 'external_indicator_id',
        'external_source', 'is_primary', 'notes',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function internalIndicator()
    {
        return $this->belongsTo(Indicator::class, 'internal_indicator_id');
    }

    public function externalIndicator()
    {
        return $this->belongsTo(Indicator::class, 'external_indicator_id');
    }
}
