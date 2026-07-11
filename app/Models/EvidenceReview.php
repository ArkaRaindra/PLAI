<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class EvidenceReview extends Model
{
    use Blameable;

    protected $table = 'evidence_reviews';

    public const array TRANSITIONS = [
        'pending' => ['approved', 'rejected', 'revision_needed'],
        'revision_needed' => ['pending'],
        'approved' => [],
        'rejected' => [],
     ];

    protected $fillable = [
        'evidence_id',
        'reviewer_id',
        'status',
        'review_notes',
        'assigned_by',
        'assigned_at',
        'reviewed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (EvidenceReview $review): void {
            if (blank($review->status)) {
                $review->status = 'pending';
            }

            if (blank($review->assigned_by)) {
                $review->assigned_by = Auth::id();
            }

            if (blank($review->assigned_at)) {
                $review->assigned_at = now();
            }
        });

        static::saving(function (EvidenceReview $review): void {
            $review->applyStatusTransitionGuard();
        });
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function applystatusTransitionGuard(): void
    {
        if (! $this->isDirty('status')) {
            return;
        }

        if ($this->exists) {
            $from = $this->getOriginal('status');
            $to = $this->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException(
                    "Transisi status review evidence dari '{$from}' ke '{$to}' tidak diizinkan"
                );
            }
        }

        if (in_array($this->status, ['approved', 'rejected', 'revision_needed'], true)) {
            $this->reviewed_at = now();
        }

        if ($this->status === 'pending') {
            $this->reviewed_at = null;
        }
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidences::class, 'evidence_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('reviewer_id', $userId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeForEvidence(Builder $query, int $evidenceId): Builder
    {
        return $query->where('evidence_id', $evidenceId);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDecided(): bool
    {
        return in_array($this->status, ['approved', 'rejected'], true);
    }
}
