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

    public function initializeWorkflow(): void
    {
        WorkflowInstance::initialize($this);
    }

    public function transitionWorkflowTo(string $status, ?string $notes = null, ?int $actedBy = null): void
    {
        $this->workflowInstance->transitionTo($status, $notes, $actedBy);
    }
}
