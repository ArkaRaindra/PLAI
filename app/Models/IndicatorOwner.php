<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorOwner extends Model
{
    protected $fillable = [
        'indicator_id', 'organization_unit_id', 'user_position_id',
        'is_primary', 'notes',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function userPosition()
    {
        return $this->belongsTo(UserPosition::class, 'user_position_id');
    }
}
