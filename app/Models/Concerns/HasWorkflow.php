<?php

namespace App\Models\Concerns;

use App\Models\WorkflowInstance;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasWorkflow
{
    public function workflowInstance(): MorphOne
    {
        return $this->morphOne(WorkflowInstance::class, 'entity');
    }

    public function initializeWorkflow(string $initialStatus = WorkflowInstance::DEFAULT_INITIAL_STATUS): void
    {
        WorkflowInstance::initialize($this, $initialStatus);
    }

    public function transitionWorkflowTo(string $status, ?string $notes = null, ?int $actedBy = null): void
    {
        $this->workflowInstance->transitionTo($status, $notes, $actedBy);
    }
}
