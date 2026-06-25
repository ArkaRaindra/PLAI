<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'indicator_id', 'quality_period_id', 'target_value',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
        ];
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class, 'target_id');
    }
}
