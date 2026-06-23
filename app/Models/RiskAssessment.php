<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'risk_id', 'probability', 'impact', 'score',
        'assessed_by', 'assessment_date',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'probability' => 'integer',
            'impact' => 'integer',
            'score' => 'integer',
            'assessment_date' => 'datetime',
        ];
    }

    public function risk()
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
