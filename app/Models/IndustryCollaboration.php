<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustryCollaboration extends Model
{
    protected $fillable = [
        'organization_unit_id', 'partner_name', 'cooperation_type',
        'start_date', 'end_date', 'description',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }
}
