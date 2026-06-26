<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $fillable = [
        'organization_unit_id', 'title', 'description',
        'current_version', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'current_version' => 'integer',
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

    public function versions()
    {
        return $this->hasMany(EvidenceVersion::class, 'evidence_id');
    }

    public function reviews()
    {
        return $this->hasMany(EvidenceReview::class, 'evidence_id');
    }

    public function links()
    {
        return $this->hasMany(EvidenceLink::class, 'evidence_id');
    }
}
