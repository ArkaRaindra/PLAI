<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectiveActionUpdate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'corrective_action_id', 'progress_percentage', 'description',
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

    public function correctiveAction()
    {
        return $this->belongsTo(CorrectiveAction::class, 'corrective_action_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
