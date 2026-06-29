<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;

class UserPosition extends Model
{
    use Blameable;

    protected $fillable = [
        'user_id', 'position_id', 'organization_unit_id',
        'start_date', 'end_date', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }
}
