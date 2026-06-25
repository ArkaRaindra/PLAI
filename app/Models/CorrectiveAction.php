<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    protected $fillable = [
        'audit_finding_id', 'decision_id', 'organization_unit_id',
        'owner_position_id', 'due_date', 'plan', 'status',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function auditFinding()
    {
        return $this->belongsTo(AuditFinding::class, 'audit_finding_id');
    }

    public function decision()
    {
        return $this->belongsTo(Decision::class, 'decision_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function ownerPosition()
    {
        return $this->belongsTo(UserPosition::class, 'owner_position_id');
    }

    public function updates()
    {
        return $this->hasMany(CorrectiveActionUpdate::class, 'corrective_action_id');
    }

    public function slaLogs()
    {
        return $this->hasMany(CapaSlaLog::class, 'corrective_action_id');
    }
}
