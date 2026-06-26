<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditCycle extends Model
{
    protected $fillable = [
        'quality_period_id', 'checklist_template_id', 'status',
        'created_by', 'updated_by',
    ];

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function checklistTemplate()
    {
        return $this->belongsTo(AuditChecklistTemplate::class, 'checklist_template_id');
    }

    public function auditAssignments()
    {
        return $this->hasMany(AuditAssignment::class, 'audit_cycle_id');
    }
}
