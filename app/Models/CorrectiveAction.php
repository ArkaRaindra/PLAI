<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorrectiveAction extends Model
{
    use Blameable;

    public const array TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => [],
    ];

    public const array EDITABLE_STATUSES =  ['draft', 'submitted'];
    
    protected $fillable = [
        'audit_finding_id',
        'decision_id',
        'organization_unit_id',
        'owner_position_id',
        'due_date',
        'plan',
        'status',
        'created_by',
        'updated-by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CorrectiveAction $correctiveAction): void {
            if (blank($correctiveAction->status)) {
                $correctiveAction->status = 'draft';
            }

            $alreadyExists = self::query()
                ->where('audit_finding_id', $correctiveAction->audit_finding_id)
                ->exists();
            
                if ($alreadyExists) {
                    throw new \RuntimeException('Audit finding ini sudah memiliki Corrective Action.');
                }
        });

        static::saving(function (CorrectiveAction $correctiveAction): void {
            if (! $correctiveAction->exists || ! $correctiveAction->isDirty('status')) {
                return;
            }

            $from = $correctiveAction->getOriginal('status');
            $to = $correctiveAction->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException("Transisi status corrective action dari '{$from}' ke '{$to}' tidak diizinkan");
            }
        });

        static::updating(function (CorrectiveAction $correctiveAction): void {
            if (! in_array($correctiveAction->getOriginal('status'), self::EDITABLE_STATUSES, true)) {
                throw new \RuntimeException('Corrective action yang sudah diverifikasi tidak dapat diubah.');
            }
        });
    }

    public function auditfinding(): BelongsTo
    {
        return $this->belongsTo(AuditFinding::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function ownerPosition():  BelongsTo
    {
        return $this->belongsTo(UserPosition::class, 'owner_position_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CorrectiveActionUpdate::class);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES, true);
    }

    public function submit(): void
    {
        $this->update(['status' => 'submitted']);
    }

    public function latestProgressPercentage(): int
    {
        return (int) ($this->updates()->orderByDesc('id')->value('progress_percentage') ?? 0);
    }

    public function canBeVerified(): bool
    {
        return $this->latestProgressPercentage() === 100;
    }
}
