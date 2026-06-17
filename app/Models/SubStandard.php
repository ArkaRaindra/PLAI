<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'standard_id',
        'code',
        'indicator',
        'max_score'
    ];

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function auditEvidences()
    {
        return $this->hasMany(AuditEvidence::class);
    }

    public function auditScores()
    {
        return $this->hasMany(AuditScore::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class, 'sub_standard_id');
    }
}
