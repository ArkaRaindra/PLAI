<?php

namespace App\Models;

use App\Blameable;
use App\Enums\QualityPeriodStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualityPeriode extends Model
{
    use Blameable;
    use HasFactory;

    protected $table = 'quality_periodes';

    protected $attributes = [
        'is_active' => true,
    ];

    protected $fillable = [
        'code', 'name', 'start_date', 'end_date', 'status', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'status' => QualityPeriodStatus::class,
        ];
    }

    public function standardVersions(): HasMany
    {
        return $this->hasMany(StandardVersion::class, 'quality_period_id');
    }
}
