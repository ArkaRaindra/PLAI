<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceLink extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidence_id', 'reference_type', 'reference_id', 'created_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'reference_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id');
    }
}
