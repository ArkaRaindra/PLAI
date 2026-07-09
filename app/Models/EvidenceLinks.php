<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EvidenceLinks extends Model
{
    use Blameable;

    protected $table = 'evidence_links';

    protected $fillable = [
        'evidence_id',
        'reference_id',
        'reference_type',
        'created_by',
        'updated_by',
    ];

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidences::class, 'evidence_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
