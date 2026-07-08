<?php

namespace App;

use App\Models\TraceabilityLinks;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTraceability
{
    public function outgoingTrace(): MorphMany
    {
        return $this->morphMany(
            TraceabilityLinks::class,
            'source'
        );
    }

    public function incomingTrace(): MorphMany
    {
        return $this->morphMany(
            TraceabilityLinks::class,
            'target'
        );
    }
}
