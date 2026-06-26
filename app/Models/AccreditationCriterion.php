<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccreditationCriterion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'framework_id', 'code', 'name',
        'created_by', 'updated_by',
    ];

    public function framework()
    {
        return $this->belongsTo(AccreditationFramework::class, 'framework_id');
    }

    public function indicatorMappings()
    {
        return $this->hasMany(AccreditationIndicatorMapping::class, 'accreditation_criteria_id');
    }
}
