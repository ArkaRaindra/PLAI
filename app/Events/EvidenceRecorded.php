<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvidenceRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Model $reference,
        public readonly int $organizationUnitId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $type,
        public readonly ?string $filePath,
        public readonly ?string $urlPath,
    ) {}
}
