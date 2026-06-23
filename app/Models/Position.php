<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'code', 'name', 'description',
        'created_by', 'updated_by',
    ];

    public function userPositions()
    {
        return $this->hasMany(UserPosition::class, 'position_id');
    }
}
