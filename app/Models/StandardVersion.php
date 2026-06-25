<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardVersion extends Model
{
    protected $fillable = [
        'standard_id', 'quality_period_id', 'version_no',
        'description', 'effective_date', 'is_active',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'version_no' => 'integer',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

    public function qualityPeriod()
    {
        return $this->belongsTo(QualityPeriod::class, 'quality_period_id');
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'standard_version_id');
    }

    public function auditChecklistTemplateItems()
    {
        return $this->hasMany(AuditChecklistTemplateItem::class, 'standard_version_id');
    }

    public function decisions()
    {
        return $this->hasMany(Decision::class, 'standard_version_id');
    }

    public function standardResponsibilities()
    {
        return $this->hasMany(StandardResponsibility::class, 'standard_version_id');
    }
}
