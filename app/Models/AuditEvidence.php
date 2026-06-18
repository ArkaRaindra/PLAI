<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditEvidence extends Model
{
    protected $fillable = [
        'user_id',
        'standard_id',
        'sub_standard_id',
        'period_id',
        'title',
        'description',
        'file_path',
        'google_drive_link',
        'status',
        'auditor_note',
    ];

    protected $table = 'audit_evidences';

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function subStandard(): BelongsTo
    {
        return $this->belongsTo(SubStandard::class);
    }

    public function subStandards(): BelongsToMany
    {
        return $this->belongsToMany(SubStandard::class, 'audit_evidence_sub_standard');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AuditScore::class);
    }

    public function getAuditorScoreAttribute(): ?float
    {
        return $this->scores()->avg('score');
    }
}
