<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Blameable;

class QualityPeriode extends Model
{
    use Blameable, HasFactory;

    protected $fillable = ['code', 'name', 'start_date', 'end_date', 'status', 'is_active', 'created_by', 'updated_by'];

    public function standardVersions(): HasMany
    {
        return $this->hasMany(StandardVersion::class);
    }
}
