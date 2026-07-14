<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceReview extends Model
{
    use Blameable;

    protected $table = 'evidence_reviews';

    protected $fillable = [
        'evidence_id',
        'status',
        'review_notes',
        'reviewed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidences::class, 'evidence_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Only evidence that are still awaiting review (submitted / review)
     * appear in the review queue. The review "status" is the evidence's
     * own workflow status.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereHas('evidence', function (Builder $query): void {
            $query->whereHas('workflowInstance', function (Builder $query): void {
                $query->whereIn('current_status', ['submitted', 'review']);
            });
        });
    }

    public function scopeForEvidence(Builder $query, int $evidenceId): Builder
    {
        return $query->where('evidence_id', $evidenceId);
    }
}
