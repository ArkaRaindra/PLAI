<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationUnit extends Model
{
    use Blameable;

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'type',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class, 'parent_id');
    }

    public function userPositions(): HasMany
    {
        return $this->hasMany(UserPosition::class, 'organization_unit_id');
    }

    public function indicatorOwners(): HasMany
    {
        return $this->hasMany(IndicatorOwner::class, 'organization_unit_id');
    }

    public function realizations(): HasMany
    {
        return $this->hasMany(Realization::class, 'organization_unit_id');
    }

    public function selfAssessments(): HasMany
    {
        return $this->hasMany(SelfAssessment::class, 'organization_unit_id');
    }
}
