<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorOwner extends Model
{
    use Blameable;

    protected $fillable = [
        'indicator_id',
        'organization_unit_id',
        'user_position_id',
        'is_primary',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function userPosition(): BelongsTo
    {
        return $this->belongsTo(UserPosition::class);
    }
}
