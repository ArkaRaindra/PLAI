<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraceabilityLink extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'source_type', 'source_id', 'target_type', 'target_id',
        'relation_type', 'notes', 'created_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'target_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
