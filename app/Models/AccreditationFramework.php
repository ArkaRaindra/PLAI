<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationFramework extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code', 'name',
        'created_by', 'updated_by',
    ];

    public function criteria()
    {
        return $this->hasMany(AccreditationCriterion::class, 'framework_id');
    }
}
