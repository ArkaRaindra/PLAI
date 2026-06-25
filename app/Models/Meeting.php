<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'quality_period_id', 'title', 'meeting_date', 'location', 'status',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'datetime',
        ];
    }

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'meeting_id');
    }

    public function decisions()
    {
        return $this->hasMany(Decision::class, 'meeting_id');
    }

    public function minutes()
    {
        return $this->hasMany(MeetingMinute::class, 'meeting_id');
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class, 'meeting_id');
    }
}
