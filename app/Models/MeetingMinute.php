<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meeting_id', 'notes', 'file_path', 'created_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}
