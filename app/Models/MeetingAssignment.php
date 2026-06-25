<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAssignment extends Model
{
    protected $fillable = [
        'decision_id', 'assignee_position_id', 'due_date', 'status', 'created_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function decision()
    {
        return $this->belongsTo(Decision::class, 'decision_id');
    }

    public function assigneePosition()
    {
        return $this->belongsTo(UserPosition::class, 'assignee_position_id');
    }

    public function updates()
    {
        return $this->hasMany(MeetingAssignmentUpdate::class, 'meeting_assignment_id');
    }
}
