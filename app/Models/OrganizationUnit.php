<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;

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

    public function parent()
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationUnit::class, 'parent_id');
    }

    public function userPositions()
    {
        return $this->hasMany(UserPosition::class, 'organization_unit_id');
    }

    public function indicatorOwners()
    {
        return $this->hasMany(IndicatorOwner::class, 'organization_unit_id');
    }
}
