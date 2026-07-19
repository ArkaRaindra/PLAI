<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditChecklistResponse extends Model
{
    use Blameable;

    protected $fillable = [
        'audit_assignment_id',
        'checklist_item_id',
        'answer',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function auditAssignment(): BelongsTo
    {
        return $this->belongsTo(AuditAssignment::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(AuditChecklistTemplateItem::class, 'checklist_item_id');
    }
}
