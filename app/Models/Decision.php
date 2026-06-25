<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    protected $fillable = [
        'meeting_id', 'standard_version_id', 'description',
        'created_by', 'updated_by',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function standardVersion()
    {
        return $this->belongsTo(StandardVersion::class, 'standard_version_id');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'decision_id');
    }

    public function meetingAssignments()
    {
        return $this->hasMany(MeetingAssignment::class, 'decision_id');
    }
}
