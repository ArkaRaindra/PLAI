<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Blameable;
use Kalnoy\Nestedset\NodeTrait;

class Standard extends Model
{
    use Blameable, HasFactory, NodeTrait;

    protected $fillable = [
        'code', 'name', 'description', 'source_type', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function standardSource()
    {
        return $this->belongsTo(StandardSource::class, 'standard_source_id');
    }
}
