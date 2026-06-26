<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapaSlaLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'corrective_action_id', 'target_date', 'completed_date', 'sla_status',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function correctiveAction()
    {
        return $this->belongsTo(CorrectiveAction::class, 'corrective_action_id');
    }
}
