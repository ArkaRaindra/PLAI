<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean'
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function auditEvidences()
    {
        return $this->hasMany(AuditEvidence::class);
    }

    public function auditScores()
    {
        return $this->hasMany(AuditScore::class);
    }
}
