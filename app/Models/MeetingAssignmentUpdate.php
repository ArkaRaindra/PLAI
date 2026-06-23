<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAssignmentUpdate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meeting_assignment_id', 'progress_percentage', 'notes',
        'updated_by', 'updated_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    public function meetingAssignment()
    {
        return $this->belongsTo(MeetingAssignment::class, 'meeting_assignment_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
