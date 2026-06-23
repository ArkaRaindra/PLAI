<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'meeting_id', 'title', 'sequence',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}
