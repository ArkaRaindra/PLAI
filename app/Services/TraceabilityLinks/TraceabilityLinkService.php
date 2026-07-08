<?php

namespace App\Services\TraceabilityLinks;

use App\Models\TraceabilityLinks;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class TraceabilityLinkService
{
    /**
     * Create a new instance.
     */
    public function __construct()
    {
        //
    }

    public function record(Model $source,
        Model $target,
        string $relationType,
        ?array $metadata = null,
        ?DateTimeInterface $performedAt = null,
    ): TraceabilityLinks {
        return TraceabilityLinks::updateOrCreate(
            [
                'source_type' => $source->getMorphClass(),
                'source_id' => $source->getKey(),

                'target_type' => $target->getMorphClass(),
                'target_id' => $target->getKey(),

                'relation_type' => $relationType,
            ],
            [
                'metadata' => $metadata,
                'performed_at' => $performedAt ?? now(),
            ],
        );
    }
}
