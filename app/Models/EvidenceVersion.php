<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenceVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidence_id', 'version_no', 'file_path', 'uploaded_by', 'uploaded_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'version_no' => 'integer',
            'uploaded_at' => 'datetime',
        ];
    }

    public function evidence()
    {
        return $this->belongsTo(Evidence::class, 'evidence_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
