<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Realization extends Model
{
    use Blameable;

    protected $table = 'realizations';

    protected $fillable = [
        'target_id',
        'organization_unit_id',
        'actual_value',
        'score',
        'notes',
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
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Realization $realization): void {
            $realization->applyStatusTransitionAudit();
        });

        static::created(function (Realization $realization): void {
            $realization->statusHistories()->create([
                'status' => 'draft',
                'action' => 'created',
                'user_id' => Auth::id(),
            ]);
        });

        static::updated(function (Realization $realization): void {
            if ($realization->wasChanged('status')) {
                $realization->statusHistories()->create([
                    'status' => $realization->status,
                    'action' => $realization->status,
                    'user_id' => Auth::id(),
                    'note' => $realization->note_rejected,
                ]);
            }
        });
    }

    public function applyStatusTransitionAudit(): void
    {
        if (! Auth::check() || ! $this->isDirty('status')) {
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

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
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

    public function statusHistories(): HasMany
    {
        return $this->hasMany(RealizationStatusHistory::class)
            ->latest();
    }
}