<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    protected $fillable = [
        'code', 'name', 'category', 'source_type', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function standardVersions()
    {
        return $this->hasMany(StandardVersion::class, 'standard_id');
    }
}
