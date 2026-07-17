<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditChecklistTemplateItem extends Model
{
    use Blameable;

    protected $fillable = [
        'template_id',
        'standard_version_id',
        'question',
        'sequence',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditChecklistTemplateItem $item): void {
            if (blank($item->sequence)) {
                $item->sequence = (int) self::query()
                    ->where('template_id', $item->template_id)
                    ->max('sequence') + 1;
            }
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AuditChecklistTemplate::class, 'template_id');
    }

    public function standardVersion(): BelongsTo
    {
        return $this->belongsTo(StandardVersion::class);
    }
}