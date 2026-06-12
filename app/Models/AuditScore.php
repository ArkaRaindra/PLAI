<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_standard_id',
        'period_id',
        'auditor_id',
        'score',
        'comment'
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subStandard()
    {
        return $this->belongsTo(SubStandard::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
