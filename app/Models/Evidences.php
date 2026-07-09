<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evidences extends Model
{
    use Blameable;

    protected $table = 'evidences';

    protected $fillable = [
        'organization_unit_id',
        'title',
        'description',
        'current_version',
        'created_by',
        'updated_by',
    ];

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
}
