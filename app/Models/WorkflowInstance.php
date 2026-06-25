<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowInstance extends Model
{
    protected $fillable = [
        'entity_type', 'entity_id', 'current_status',
        'created_by', 'updated_by',
    ];

    public function histories()
    {
        return $this->hasMany(WorkflowHistory::class, 'workflow_instance_id');
    }
}
