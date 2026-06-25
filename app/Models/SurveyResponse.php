<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'survey_id', 'respondent_identifier', 'total_score', 'submitted_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function details()
    {
        return $this->hasMany(SurveyResponseDetail::class, 'response_id');
    }
}
