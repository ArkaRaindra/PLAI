<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskMitigation extends Model
{
    protected $fillable = [
        'risk_id', 'owner_position_id', 'due_date',
        'mitigation_plan', 'status',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function ownerPosition()
    {
        return $this->belongsTo(UserPosition::class, 'owner_position_id');
    }
}
