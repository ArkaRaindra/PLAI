<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditChecklistResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'audit_assignment_id', 'checklist_item_id', 'answer', 'notes', 'created_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function auditAssignment()
    {
        return $this->belongsTo(AuditAssignment::class, 'audit_assignment_id');
    }

    public function checklistItem()
    {
        return $this->belongsTo(AuditChecklistTemplateItem::class, 'checklist_item_id');
    }
}
