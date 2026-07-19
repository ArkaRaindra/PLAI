<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditChecklistTemplate extends Model
{
    use Blameable;

    protected $fillable = [
        'name',
        'version_no',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AuditChecklistTemplateItem::class, 'template_id')->orderBy('sequence');
    }

    public function auditCycles(): HasMany
    {
        return $this->hasMany(AuditCycle::class, 'checklist_template_id');
    }
}
