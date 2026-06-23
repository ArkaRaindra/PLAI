<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfAssessment extends Model
{
    protected $fillable = [
        'organization_unit_id', 'quality_period_id', 'final_score',
        'summary', 'status', 'submitted_by', 'submitted_at',
        'approved_by', 'approved_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'final_score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(SelfAssessmentDetail::class, 'self_assessment_id');
    }
}
