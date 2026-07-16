<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditCycle extends Model
{
    use Blameable;

    protected $table = 'audit_cycles';

    protected $fillable = [
        'quality_period_id',
        'checklist_template_id',
        'status',
        'created_by',
        'updated_by',
    ];
}