<?php

namespace App\Models;

use App\Blameable;
use App\Models\Concerns\HasWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * CAPA-001..003.
 *
 * As of CAPA-003 (Issue #45), the status lifecycle is driven by the
 * generic `workflow_instances` / `workflow_histories` engine (the same
 * one used by Evidence and the Auditor Workflow) instead of an inline
 * transition guard, because "Verification History" requires a real audit
 * trail per transition — something a bare status column can't give us.
 *
 * The `status` column from the ERD is kept and mirrored on every
 * transition purely so existing simple queries/badges (statusLabels(),
 * table columns, etc.) keep working without joining workflow_instances
 * every time; `workflowInstance.current_status` remains the source of
 * truth.
 */
class CorrectiveAction extends Model
{
    use Blameable;
    use HasWorkflow;

    public const array EDITABLE_STATUSES = ['draft', 'submitted'];

    private const array USER_EDITABLE_FIELDS = ['plan', 'due_date', 'owner_position_id'];

    protected $fillable = [
        'audit_finding_id',
        'decision_id',
        'organization_unit_id',
        'owner_position_id',
        'due_date',
        'plan',
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

        static::created(function (CorrectiveAction $correctiveAction): void {
            $correctiveAction->initializeWorkflow('draft');
        });

        static::updating(function (CorrectiveAction $correctiveAction): void {
            $editingBusinessFields = collect(self::USER_EDITABLE_FIELDS)
                ->contains(fn (string $field): bool => $correctiveAction->isDirty($field));

            if ($editingBusinessFields && ! in_array($correctiveAction->getOriginal('status'), self::EDITABLE_STATUSES, true)) {
                throw new \RuntimeException('Corrective action yang sudah masuk tahap verifikasi tidak dapat diubah.');
            }
        });
    }

    public function auditFinding(): BelongsTo
    {
        return $this->belongsTo(AuditFinding::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function ownerPosition(): BelongsTo
    {
        return $this->belongsTo(UserPosition::class, 'owner_position_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CorrectiveActionUpdate::class);
    }

    public function latestProgressPercentage(): int
    {
        return (int) ($this->updates()->orderByDesc('id')->value('progress_percentage') ?? 0);
    }

    public function canBeVerified(): bool
    {
        return $this->latestProgressPercentage() === 100;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES, true);
    }

    private function moveTo(string $status, ?string $notes = null): void
    {
        WorkflowInstance::initialize($this, $this->status)->transitionTo($status, $notes);

        self::query()->whereKey($this->id)->update([
            'status' => $status,
            'updated_by' => Auth::id() ?? 'system',
        ]);

        $this->setAttribute('status', $status);
    }

    public function submit(): void
    {
        $this->moveTo('submitted');
    }

    public function startVerification(): void
    {
        if (! $this->canBeVerified()) {
            throw new \RuntimeException('Progress belum mencapai 100%, verifikasi belum dapat dimulai.');
        }

        $this->moveTo('verification');
    }
    
    public function approve(?string $notes = null): void
    {
        $this->moveTo('closed', $notes);
    }

    public function reject(string $notes): void
    {
        $this->moveTo('submitted', $notes);
    }

    public function reopen(string $notes): void
    {
        $this->moveTo('submitted', $notes);
    }
}