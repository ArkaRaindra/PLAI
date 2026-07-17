<?php

namespace App\Models;

use App\Blameable;
use App\Models\Concerns\HasWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evidences extends Model
{
    use Blameable;
    use HasWorkflow;

    protected $table = 'evidences';

    protected $fillable = [
        'organization_unit_id',
        'title',
        'description',
        'type',
        'file_path',
        'url_path',
        'current_version',
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
        static::created(function (Evidences $evidence): void {
            $evidence->initializeWorkflow();
        });
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function evidenceVersions(): HasMany
    {
        return $this->hasMany(EvidenceVersions::class, 'evidence_id');
    }

    public function evidenceLinks(): HasMany
    {
        return $this->hasMany(EvidenceLinks::class, 'evidence_id');
    }

    public function evidenceReviews(): HasMany
    {
        return $this->hasMany(EvidenceReview::class, 'evidence_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($keyword): void {
            $inner->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    public function scopeUnit(Builder $query, int|string|null $organizationUnitId): Builder
    {
        if (blank($organizationUnitId)) {
            return $query;
        }

        return $query->where('organization_unit_id', $organizationUnitId);
    }

    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->whereHas(
            'workflowInstance',
            fn (Builder $inner) => $inner->where('current_status', $status),
        );
    }

    public function auditChecklistResponses(): HasMany
    {
        return $this->hasMany(AuditChecklistResponse::class, 'evidence_id');
    }
}
