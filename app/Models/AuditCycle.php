<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditCycle extends Model
{
    use Blameable;

    public const array TRANSITIONS = [
        'draft' => ['ongoing', 'cancelled'],
        'ongoing' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    protected $fillable = [
        'quality_period_id',
        'checklist_template_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditCycle $cycle): void {
            $period = QualityPeriod::query()->find($cycle->quality_period_id);

            if ($period && $period->status?->value !== 'active') {
                throw new \RuntimeException(
                   'Audit cycle hanya dapat dibuat untuk periode mutu yang berstatus aktif.'
                );
            }
        });

        static::saving(function (AuditCycle $cycle): void {
            if (! $cycle->exists || ! $cycle->isDirty('status')) {
                return;
            }

            $from = $cycle->getOriginal('status');
            $to = $cycle->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException(
                    "Transisi status dari '{$from}' ke '{$to}' tidak diizinkan"
                );
            }
        });
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function qualityPeriod(): BelongsTo
    {
        return $this->belongsTo(QualityPeriod::class);
    }

    public function checklistTemplate(): BelongsTo
    {
        return $this->belongsTo(AuditChecklistTemplate::class, 'checklist_template_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AuditAssignment::class);
    }
}