<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'audit_cycle_id', 'auditor_position_id', 'organization_unit_id',
        'assigned_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function auditCycle()
    {
        return $this->belongsTo(AuditCycle::class, 'audit_cycle_id');
    }

    public function auditorPosition()
    {
        return $this->belongsTo(UserPosition::class, 'auditor_position_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function findings()
    {
        return $this->hasMany(AuditFinding::class, 'audit_assignment_id');
    }

    public function checklistResponses()
    {
        return $this->hasMany(AuditChecklistResponse::class, 'audit_assignment_id');
    }
}
