<?php

namespace App\Models;

use App\Blameable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class WorkflowInstance extends Model
{
    use Blameable;

    protected $table = 'workflow_instances';

    public const array TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['review'. 'rejected'],
        'review' => ['approved', 'rejected'],
        'rejected' => ['draft'],
        'approved' => ['published'],
        'published' => [],
    ];

    protected $fillable = [
        'entitu_type',
        'entity_id',
        'current_status',
        'workflow_type',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'published_by',
        'oublished_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'published_at' => 'datetime,'
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (WorkflowInstance $instance): void {
            $instance->applyStatusTransitionAudit();
        });

        static::created(function (WorkflowInstance $instance): void {
            $instance->histories()->create([
                'from_status' => null,
                'status' => $instance->current_status,
                'action' => 'created',
                'acted_by' => Auth::id(),
                'acted_at' => now(),
            ]);
        });

        static::updated(function (WorkflowInstance $instance): void {
            if ($instance->wasChanged('current_status')) {
                $instance->histories()->create([
                    'from_status' => $instance->getOriginal('current_status'),
                    'status' => $instance->current_status,
                    'action' => $instance->current_status,
                    'acted_by' => Auth::id(),
                    'acted_at' => now(),
                    'notes' => $instance->current_status === 'rejected' ? $instance->rejection_reason : null,
                ]);
            }
        });
    }

    public static function openFor(Model $entity, string $workflowType = 'evidence_approval'): self
    {
        return static::query()->firstOrCreate([
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'workflow_type' => $workflowType,
        ], [
            'current_status' => 'draft',
        ]);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->current_status] ?? [], true);
    }

    public function applyStatusTransitionAudit(): void
    {
        if (! $this->isDirty('current_status')) {
            return;
        }

        if ($this->exists) {
            $from = $this->getOriginal('current_status');
            $to = $this->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException(
                    "Transisi status workflow dari '{$from}' ke '{$to}' tidak diizinkan"
                );
            }
        }

        $userId = Auth::id();

        if ($this->current_status === 'submitted') {
            $this->submitted_by = $userId;
            $this->submitted_at = now();
        }

        if ($this->current_status === 'review') {
            $this->reviewed_by = $userId;
            $this->reviewed_at = now();
        }

        if ($this->current_status === 'approved') {
            $this->approved_by = $userId;
            $this->approved_at = now();
        }

        if ($this->current_status === 'rejected') {
            $this->rejected_by = $userId;
            $this->rejected_at = now();
        }

        if ($this->current_status === 'published') {
            $this->published_by = $userId;
            $this->published_at = now();
        }
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class)->latest('acted_at');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
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
