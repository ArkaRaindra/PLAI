<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Indicator extends Model
{
    protected $fillable = [
        'code',
        'sub_standard_id',
        'description',
    ];

    public function subStandard(): BelongsTo
    {
        return $this->belongsTo(SubStandard::class, 'sub_standard_id');
    }
}
