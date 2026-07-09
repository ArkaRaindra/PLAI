<?php

namespace App\Listeners;

use App\Events\EvidenceRecorded;
use App\Services\Evidence\EvidenceService;

class RecordEvidenceListener
{
    public function __construct(protected EvidenceService $evidenceService) {}

    public function handle(EvidenceRecorded $event): void
    {
        $this->evidenceService->record(
            reference: $event->reference,
            organizationUnitId: $event->organizationUnitId,
            title: $event->title,
            description: $event->description,
            type: $event->type,
            filePath: $event->filePath,
            urlPath: $event->urlPath,
        );
    }
}
