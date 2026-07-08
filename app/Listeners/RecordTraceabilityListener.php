<?php

namespace App\Listeners;

use App\Events\TraceabilityRecorded;
use App\Services\TraceabilityLinks\TraceabilityLinkService;

class RecordTraceabilityListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected TraceabilityLinkService $traceabilityService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TraceabilityRecorded $event): void
    {
        $this->traceabilityService->record(
            source: $event->source,
            target: $event->target,
            relationType: $event->relationType,
            metadata: $event->metadata,
            performedAt: $event->performedAt,
        );
    }
}
