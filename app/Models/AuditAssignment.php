<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditAssignment extends Model
{
    use Blameable;

    protected $fillable = [
        'audit_cycle_id',
        'auditor_position_id',
        'organization_unit_id',
        'assigned_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditAssignment $assignment): void {
            if (blank($assignment->assigned_at)) {
                $assignment->assigned_at = now();
            }
        });
    }

    public function auditCycle(): BelongsTo
    {
        return $this->belongsTo(AuditCycle::class);
    }

    public function auditorPosition(): BelongsTo
    {
        return $this->belongsTo(UserPosition::class, 'auditor_position_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AuditChecklistResponse::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AuditFinding::class);
    }
}