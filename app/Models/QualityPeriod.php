<?php

namespace App\Models;

use App\Blameable;
use App\Enums\QualityPeriodStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityPeriod extends Model
{
    use Blameable;
    use HasFactory;

    protected $attributes = [
        'is_active' => false,
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
