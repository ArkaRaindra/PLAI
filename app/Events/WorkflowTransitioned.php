<?php

namespace App\Events;

use App\Models\WorkflowInstance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowTransitioned
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly WorkflowInstance $workflowInstance,
        public readonly string $fromStatus,
        public readonly string $toStatus,
    ) {
        //
    }
}
