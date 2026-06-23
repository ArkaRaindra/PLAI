<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponseDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'response_id', 'question_id', 'score', 'comment',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function response()
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }
}
