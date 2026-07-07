<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardSource extends Model
{
    use Blameable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'is_external',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function standards()
    {
        return $this->hasMany(Standard::class, 'standard_source_id');
    }
}
