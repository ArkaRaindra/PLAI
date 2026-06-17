<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditEvidence extends Model
{
    protected $fillable = [
        'user_id',
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

    public function subStandard()
    {
        return $this->belongsTo(SubStandard::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function scores()
    {
        return $this->hasMany(AuditScore::class, 'sub_standard_id', 'sub_standard_id')
            ->where('user_id', $this->user_id)
            ->where('period_id', $this->period_id);
    }

    public function getAuditorScoreAttribute()
    {
        return $this->scores()->first()?->score;
    }
}
