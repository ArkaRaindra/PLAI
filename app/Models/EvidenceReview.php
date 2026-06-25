<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceReview extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidence_id', 'reviewer_id', 'status', 'review_notes', 'reviewed_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
