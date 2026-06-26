<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditFinding extends Model
{
    protected $fillable = [
        'audit_assignment_id', 'indicator_id', 'title', 'category',
        'severity', 'description', 'root_cause', 'recommendation',
        'due_date', 'status',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function auditAssignment()
    {
        return $this->belongsTo(AuditAssignment::class, 'audit_assignment_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'audit_finding_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class, 'audit_finding_id');
    }
}
