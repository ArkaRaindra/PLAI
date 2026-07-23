<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPosition extends Model
{
    use Blameable;

    protected $fillable = [
        'user_id',
        'position_id',
        'organization_unit_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function indicatorOwners(): HasMany
    {
        return $this->hasMany(IndicatorOwner::class, 'user_position_id');
    }

    public function auditAssignments(): HasMany
    {
        return $this->hasMany(AuditAssignment::class, 'auditor_position_id');
    }
}
