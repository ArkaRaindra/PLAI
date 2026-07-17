<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditChecklistTemplate extends Model
{
    use Blameable;

    protected $fillable = [
        'code',
        'name',
        'version',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'code', 'code')->orderByDesc('id');
    }
}