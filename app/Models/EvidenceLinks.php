<?php

namespace App\Models;

use App\Blameable;
use App\Support\EvidenceLink\LinkableTypeRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EvidenceLinks extends Model
{
    use Blameable;

    protected $table = 'evidence_links';

    protected $fillable = [
        'evidence_id',
        'reference_id',
        'reference_type',
        'created_by',
        'updated_by',
    ];

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidences::class, 'evidence_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function referenceTypeLabel(): string
    {
        return LinkableTypeRegistry::labelFor($this->reference_type);
    }

    public function referenceTitle(): ?string
    {
        $model = $this->reference;

        if ($model === null) {
            return null;
        }

        $titleAttribute = LinkableTypeRegistry::titleAttributeFor($this->reference_type);

        return $model->{$titleAttribute} ?? "#{$this->reference_id}";
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}