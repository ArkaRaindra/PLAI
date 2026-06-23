<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditChecklistTemplateItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'template_id', 'standard_version_id', 'question', 'sequence',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    public function template()
    {
        return $this->belongsTo(AuditChecklistTemplate::class, 'template_id');
    }

    public function standardVersion()
    {
        return $this->belongsTo(StandardVersion::class, 'standard_version_id');
    }

    public function auditChecklistResponses()
    {
        return $this->hasMany(AuditChecklistResponse::class, 'checklist_item_id');
    }
}
