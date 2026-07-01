<?php

namespace App\Models;

use App\Blameable;
use App\Enums\QualityPeriodStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandardVersion extends Model
{
    use Blameable;
    use HasFactory;

    protected $fillable = [
        'standard_id', 'quality_period_id', 'version', 'start_date', 'end_date',
        'status', 'is_active', 'created_by', 'updated_by',
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

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function qualityPeriod(): BelongsTo
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }
}
