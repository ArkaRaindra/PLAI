<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_evidence_id',
        'user_id',
        'sub_standard_id',
        'period_id',
        'auditor_id',
        'score',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function auditEvidence(): BelongsTo
    {
        return $this->belongsTo(AuditEvidence::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subStandard(): BelongsTo
    {
        return $this->belongsTo(SubStandard::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
