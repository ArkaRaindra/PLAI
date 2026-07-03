<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealizationStatusHistory extends Model
{
    protected $fillable = [
        'realization_id',
        'status',
        'action',
        'user_id',
        'note',
    ];

    public function realization()
    {
        return $this->belongsTo(Realization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
