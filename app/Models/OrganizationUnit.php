<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationUnit extends Model
{
    protected $fillable = [
        'parent_id', 'code', 'name', 'type', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(OrganizationUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationUnit::class, 'parent_id');
    }

    public function userPositions()
    {
        return $this->hasMany(UserPosition::class, 'organization_unit_id');
    }

    public function indicatorOwners()
    {
        return $this->hasMany(IndicatorOwner::class, 'organization_unit_id');
    }

    public function realizations()
    {
        return $this->hasMany(Realization::class, 'organization_unit_id');
    }

    public function selfAssessments()
    {
        return $this->hasMany(SelfAssessment::class, 'organization_unit_id');
    }

    public function qualityDocuments()
    {
        return $this->hasMany(QualityDocument::class, 'organization_unit_id');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'organization_unit_id');
    }

    public function auditAssignments()
    {
        return $this->hasMany(AuditAssignment::class, 'organization_unit_id');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'organization_unit_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'organization_unit_id');
    }

    public function industryCollaborations()
    {
        return $this->hasMany(IndustryCollaboration::class, 'organization_unit_id');
    }

    public function dashboardSnapshots()
    {
        return $this->hasMany(DashboardSnapshot::class, 'organization_unit_id');
    }
}
