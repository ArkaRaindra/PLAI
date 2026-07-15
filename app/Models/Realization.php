<?php

namespace App\Models;

use App\Blameable;
use App\HasTraceability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Realization extends Model
{
    use Blameable, HasTraceability;

    protected $table = 'realizations';

    public const array TRANSITIONS = [
        'draft' => ['submitted'],
        'submitted' => ['approved', 'rejected'],
        'rejected' => ['submitted'],
        'approved' => [],
    ];

    protected $fillable = [
        'target_id',
        'organization_unit_id',
        'actual_value',
        'score',
        'notes',
        'note_rejected',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Realization $realization): void {
            $realization->applyStatusTransitionAudit();
        });

        static::created(function (Realization $realization): void {
            $realization->statusHistories()->create([
                'status' => 'draft',
                'action' => 'created',
                'user_id' => Auth::id(),
            ]);
        });

        static::updated(function (Realization $realization): void {
            if ($realization->wasChanged('status')) {
                $realization->statusHistories()->create([
                    'status' => $realization->status,
                    'action' => $realization->status,
                    'user_id' => Auth::id(),
                    'note' => $realization->note_rejected,
                ]);
            }
        });
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function applyStatusTransitionAudit(): void
    {
        if (! $this->isDirty('status')) {
            return;
        }

        if ($this->exists) {
            $from = $this->getOriginal('status');
            $to = $this->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw new \RuntimeException(
                    "Transisi status realisasi dari '{$from}' ke '{$to} tidak diizinkan'"
                );
            }
        }

        $userId = Auth::id();

        if ($this->status === 'submitted') {
            $this->submitted_by = $userId;
            $this->submitted_at = now();
        }

        if ($this->status === 'approved') {
            $this->approved_by = $userId;
            $this->approved_at = now();
        }

        if ($this->status === 'rejected') {
            $this->rejected_by = $userId;
            $this->rejected_at = now();
        }
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(RealizationStatusHistory::class)
            ->latest();
    }

    public function selfAssessmentDetail(): HasOne
    {
        return $this->hasOne(SelfAssessmentDetail::class);
    }

    public function evidenceLinks(): MorphMany
    {
        return $this->morphMany(EvidenceLinks::class, 'reference');
    }

    public function evidenceLink(): MorphOne
    {
        return $this->morphOne(EvidenceLinks::class, 'reference');
    }

    public function evidenceVersionsQuery(): Builder
    {
        $this->loadMissing('evidenceLink');

        $evidenceId = $this->evidenceLink?->evidence_id;

        if ($evidenceId === null) {
            return EvidenceVersions::query()->whereKey([]);
        }

        return EvidenceVersions::query()
            ->where('evidence_id', $evidenceId)
            ->with(['evidence.evidenceVersions', 'uploadedBy'])
            ->orderByDesc('id');
    }

    public function latestEvidenceVersion(): ?EvidenceVersions
    {
        $this->loadMissing('evidenceLink.evidence');

        $evidence = $this->evidenceLink?->evidence;

        if ($evidence === null) {
            return null;
        }

        return $evidence->evidenceVersions()
            ->where('version', $evidence->current_version)
            ->first()
            ?? $evidence->evidenceVersions()->latest('id')->first();
    }

    public function canManageEvidence(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function evidenceFormData(): ?array
    {
        $this->loadMissing('evidenceLink.evidence');

        $evidence = $this->evidenceLink?->evidence;
        $version = $this->latestEvidenceVersion();

        if ($evidence === null || $version === null) {
            return null;
        }

        return [
            'title' => $evidence->title,
            'description' => $evidence->description,
            'type' => $version->type,
            'file_path' => $version->file_path,
            'url_path' => $version->url_path,
        ];
    }

    protected function displayTitle(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $this->loadMissing('target.indicator', 'target.qualityPeriod');

                $indicatorName = $this->target?->indicator?->name;
                $periodName = $this->target?->qualityPeriod?->name;

                $label = $indicatorName ?? "Realisasi #{$this->id}";

                return $periodName !== null ? "{$label} ({$periodName})" : $label;
            },
        );
    }
}