<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSnapshot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quality_period_id', 'organization_unit_id', 'metrics', 'generated_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'json',
            'generated_at' => 'datetime',
        ];
    }

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }
}
