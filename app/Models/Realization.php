<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realization extends Model
{
    protected $fillable = [
        'target_id', 'organization_unit_id', 'actual_value',
        'score', 'notes', 'status',
        'submitted_by', 'submitted_at', 'approved_by', 'approved_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'actual_value' => 'decimal:2',
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function target()
    {
        return $this->belongsTo(Target::class, 'target_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
