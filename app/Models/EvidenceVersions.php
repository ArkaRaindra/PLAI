<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceVersions extends Model
{
    use Blameable;

    protected $table = 'evidence_versions';

    protected $fillable = [
        'evidence_id',
        'version',
        'type',
        'file_path',
        'url_path',
        'uploaded_by',
        'created_by',
        'updated_by',
    ];

    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidences::class, 'evidence_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
