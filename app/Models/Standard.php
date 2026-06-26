<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;

    public $fillable = [
        'code',
        'name',
        'description',
        'weight',
    ];

    public function subStandards()
    {
        return $this->hasMany(SubStandard::class);
    }

    public function standardVersions()
    {
        return $this->hasMany(StandardVersion::class, 'standard_id');
    }
}
