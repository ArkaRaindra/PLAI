<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $fillable = [
        'standard_version_id', 'parent_id', 'code', 'name',
        'description', 'measurement_formula', 'unit', 'weight',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    public function standardVersion()
    {
        return $this->belongsTo(StandardVersion::class, 'standard_version_id');
    }

    public function parent()
    {
        return $this->belongsTo(Indicator::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Indicator::class, 'parent_id');
    }

    public function indicatorMappings()
    {
        return $this->hasMany(IndicatorMapping::class, 'internal_indicator_id');
    }

    public function externalMappings()
    {
        return $this->hasMany(IndicatorMapping::class, 'external_indicator_id');
    }

    public function indicatorOwners()
    {
        return $this->hasMany(IndicatorOwner::class, 'indicator_id');
    }

    public function targets()
    {
        return $this->hasMany(Target::class, 'indicator_id');
    }

    public function selfAssessmentDetails()
    {
        return $this->hasMany(SelfAssessmentDetail::class, 'indicator_id');
    }

    public function auditFindings()
    {
        return $this->hasMany(AuditFinding::class, 'indicator_id');
    }

    public function accreditationMappings()
    {
        return $this->hasMany(AccreditationIndicatorMapping::class, 'indicator_id');
    }
}
