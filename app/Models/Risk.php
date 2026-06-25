<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $fillable = [
        'organization_unit_id', 'audit_finding_id', 'code', 'title',
        'description', 'category', 'risk_owner_position_id', 'status',
        'created_by', 'updated_by',
    ];

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function auditFinding()
    {
        return $this->belongsTo(AuditFinding::class, 'audit_finding_id');
    }

    public function riskOwnerPosition()
    {
        return $this->belongsTo(UserPosition::class, 'risk_owner_position_id');
    }

    public function assessments()
    {
        return $this->hasMany(RiskAssessment::class, 'risk_id');
    }

    public function mitigations()
    {
        return $this->hasMany(RiskMitigation::class, 'risk_id');
    }
}
