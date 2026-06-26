<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfAssessmentDetail extends Model
{
    protected $fillable = [
        'self_assessment_id', 'indicator_id', 'score',
        'analysis', 'strength', 'weakness',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function selfAssessment()
    {
        return $this->belongsTo(SelfAssessment::class, 'self_assessment_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }
}
