<?php

namespace App\Models;

use App\Blameable;
use App\Enums\CalculationMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'standard_version_id', 'parent_indicator_id', 'code', 'name', 'description', 'calculation_method', 'measurement_unit', 'weight',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'calculation_method' => CalculationMethod::class,
            'weight' => 'decimal:2',
        ];
    }

    public function parentIndicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'parent_indicator_id');
    }

    public function standardVersion(): BelongsTo
    {
        return $this->belongsTo(StandardVersion::class);
    }

    public function indicatorOwners(): HasMany
    {
        return $this->hasMany(IndicatorOwner::class);
    }

    public function indicatorMappingsInternal(): HasMany
    {
        return $this->hasMany(IndicatorMapping::class, 'internal_indicator_id');
    }

    public function indicatorMappingsExternal(): HasMany
    {
        return $this->hasMany(IndicatorMapping::class, 'external_indicator_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }
}
