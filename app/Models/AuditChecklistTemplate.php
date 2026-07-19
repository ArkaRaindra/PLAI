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

    protected static function booted(): void
    {
        static::creating(function (AuditChecklistTemplate $template): void {
            if (blank($template->version_no)) {
                $template->version_no = '1.0';
            }
        });
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
