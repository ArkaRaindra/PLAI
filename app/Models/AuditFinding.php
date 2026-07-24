<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditFinding extends Model
{
    use Blameable;

    protected $fillable = [
        'audit_assignment_id',
        'indicator_id',
        'title',
        'category',
        'severity',
        'description',
        'root_cause',
        'recommendation',
        'due_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function auditAssignment(): BelongsTo
    {
        return $this->belongsTo(AuditAssignment::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function close(): void
    {
        if (! $this->correctiveAction()->exists()) {
            throw new \RuntimeException('Temuan tidak dapat ditutup karena belum memiliki Corrective Action.');
        }

        $this->update(['status' => 'closed']);
    }
}
