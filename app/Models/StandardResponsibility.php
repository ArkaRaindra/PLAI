<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardResponsibility extends Model
{
    protected $fillable = [
        'standard_version_id', 'ppepp_stage', 'user_position_id',
        'created_by', 'updated_by',
    ];

    public function standardVersion()
    {
        return $this->belongsTo(StandardVersion::class, 'standard_version_id');
    }

    public function userPosition()
    {
        return $this->belongsTo(UserPosition::class, 'user_position_id');
    }
}
