<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'workflow_instance_id', 'status', 'notes', 'acted_by', 'acted_at',
        'created_by', 'updated_by',
    ];

    public function workflowInstance()
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
