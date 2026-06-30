<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StandardVersion extends Model
{
    use Blameable, HasFactory;

    protected $fillable = ['standard_id', 'quality_period_id', 'version', 'start_date', 'end_date', 'status', 'is_active', 'created_by', 'updated_by'];

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }
    
    public function qualityPeriod(): BelongsTo
    {
        return $this->belongsTo(QualityPeriod::class);
    }   
}
