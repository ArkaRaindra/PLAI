<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPosition extends Model
{
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

    public function indicatorOwners()
    {
        return $this->hasMany(IndicatorOwner::class, 'user_position_id');
    }

    public function auditAssignments()
    {
        return $this->hasMany(AuditAssignment::class, 'auditor_position_id');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'owner_position_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'risk_owner_position_id');
    }

    public function riskMitigations()
    {
        return $this->hasMany(RiskMitigation::class, 'owner_position_id');
    }

    public function meetingAssignments()
    {
        return $this->hasMany(MeetingAssignment::class, 'assignee_position_id');
    }

    public function standardResponsibilities()
    {
        return $this->hasMany(StandardResponsibility::class, 'user_position_id');
    }
}
