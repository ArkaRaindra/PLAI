<?php

namespace App\Models;

use App\Blameable;
use App\Models\Concerns\HasWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuditFinding extends Model
{
    use Blameable;
    use HasWorkflow;

    public function getMorphClass(): string
    {
        return 'audit_finding';
    }

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

    protected static function booted(): void
    {
        static::created(function (AuditFinding $finding): void {
            $finding->initializeWorkflow(initialStatus: 'open');
        });
    }

    public function auditAssignment(): BelongsTo
    {
        return $this->belongsTo(AuditAssignment::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function correctiveAction(): HasOne
    {
        return $this->hasOne(CorrectiveAction::class);
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
