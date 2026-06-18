<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuditEvidence extends Model
{
    protected $fillable = [
        'user_id',
        'standard',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function subStandard()
    {
        return $this->belongsTo(SubStandard::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(AuditScore::class);
    }

    public function scores(): HasOne
    {
        return $this->score();
    }

    public function getAuditorScoreAttribute(): ?int
    {
        return $this->score?->score;
    }
}
