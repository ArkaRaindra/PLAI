<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityDocument extends Model
{
    protected $fillable = [
        'category', 'title', 'document_number', 'version_no',
        'effective_date', 'file_path', 'status',
        'organization_unit_id', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'version_no' => 'integer',
            'effective_date' => 'date',
        ];
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class, 'organization_unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
