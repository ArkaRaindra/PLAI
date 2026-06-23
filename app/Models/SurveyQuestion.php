<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'survey_id', 'question_text', 'sequence',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function options()
    {
        return $this->hasMany(SurveyQuestionOption::class, 'question_id');
    }

    public function responseDetails()
    {
        return $this->hasMany(SurveyResponseDetail::class, 'question_id');
    }
}
