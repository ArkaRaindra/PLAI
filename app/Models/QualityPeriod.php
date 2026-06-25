<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityPeriod extends Model
{
    protected $fillable = [
        'code', 'name', 'start_date', 'end_date', 'status', 'is_active',
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

    public function standardVersions()
    {
        return $this->hasMany(StandardVersion::class, 'quality_period_id');
    }

    public function targets()
    {
        return $this->hasMany(Target::class, 'quality_period_id');
    }

    public function selfAssessments()
    {
        return $this->hasMany(SelfAssessment::class, 'quality_period_id');
    }

    public function auditCycles()
    {
        return $this->hasMany(AuditCycle::class, 'quality_period_id');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'quality_period_id');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'quality_period_id');
    }

    public function dashboardSnapshots()
    {
        return $this->hasMany(DashboardSnapshot::class, 'quality_period_id');
    }
}
