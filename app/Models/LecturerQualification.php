<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerQualification extends Model
{
    protected $fillable = [
        'user_id', 'nidn', 'education_level', 'functional_rank',
        'is_certified', 'competency_certification',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_certified' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
