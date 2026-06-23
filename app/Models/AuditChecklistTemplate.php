<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditChecklistTemplate extends Model
{
    protected $fillable = [
        'name', 'version_no',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'version_no' => 'integer',
        ];
    }

    public function items()
    {
        return $this->hasMany(AuditChecklistTemplateItem::class, 'template_id');
    }

    public function auditCycles()
    {
        return $this->hasMany(AuditCycle::class, 'checklist_template_id');
    }
}
