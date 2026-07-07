<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class SelfAssessment extends Model
{
    use Blameable;

    protected $table = 'self_assessments';

    public const array TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['approved', 'rejected'],
        'rejected' => ['submitted'],
        'approved' => [],
    ];

    protected $fillable = [
        'organization_unit_id',
        'quality_period_id',
        'final_score',
        'summary',
        'note_rejected',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'final_score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SelfAssessment $selfAssessment): void {
            $selfAssessment->applyStatusTransitionAudit();
        });
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function applyStatusTransitionAudit(): void
    {
        if (! $this->isDirty('status')) {
            return;
        }

        if ($this->exists) {
            $from = $this->getOriginal('status');
            $to = $this->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException(
                    "Transisi status self assessment dari '{$from}' ke '{$to}' tidak diizinkan."
                );
            }
        }

        if (! Auth::check()) {
            return;
        }

        $userId = Auth::id();

        if ($this->status === 'submitted') {
            $this->submitted_by = $userId;
            $this->submitted_at = now();
        }

        if ($this->status === 'approved') {
            $this->approved_by = $userId;
            $this->approved_at = now();
        }

        if ($this->status === 'rejected') {
            $this->rejected_by = $userId;
            $this->rejected_at = now();
        }
    }

    public function recalculateFinalScore(): void
    {
        $average = $this->details()->avg('score');

        $this->final_score = $average !== null ? round((float) $average, 2) : null;
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function qualityPeriod(): BelongsTo
    {
        return $this->belongsTo(QualityPeriod::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SelfAssessmentDetail::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
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
