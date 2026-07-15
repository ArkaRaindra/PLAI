<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
            'version_no' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AuditChecklistTemplate::class, 'template_id')->orderBy('sequence');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('id', function ($sub): void {
            $sub->selectRaw('MAX(t2.id)')
                ->from('audit_checklist_templates as t2')
                ->whereColumn('t2.name', 'audit_checklist_templates.name');
        });
    }

    public function isActiveVersion(): bool
    {
        $latestVersionNo = static::query()
            ->where('name', $this->name)
            ->max('version_no');
        
        return (int) $latestVersionNo === (int) $this->version_no;
    }

    public static function createNewVersion(self $template): self
    {
        return DB::transaction(function () use ($template): self{
            $nextVersionNo = ((int) static::query()
                ->where('name', $template->name)
                ->max('version_no')) + 1;

            $newTemplate = static::query()->create([
                'name' => $template->name,
                'version_no' => $nextVersionNo,
            ]);

            foreach ($template->items()->orderBy('sequence')->get() as $item) {
                $newTemplate->items()->create([
                    'standard_version_id' => $item->standard_version_id,
                    'question' => $item->question,
                    'sequence' => $item->sequence,
                ]);
            }

            return $newTemplate;
        });
    }
}
