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
        static::created(function (Realization $realization) {
        $realization->statusHistories()->create([
            'status' => 'draft',
            'action' => 'created',
            'user_id' => auth()->id(),
        ]);
    });

    static::updated(function (Realization $realization) {
        if ($realization->wasChanged('status')) {
            $realization->statusHistories()->create([
                'status' => $realization->status,
                'action' => $realization->status,
                'user_id' => auth()->id(),
                'note' => $realization->note_rejected,
            ]);
        }
    });
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
