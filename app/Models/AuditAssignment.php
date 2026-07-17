<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditAssignment extends Model
{
    use Blameable;

    protected $fillable = [
        'audit_cycle_id',
        'auditor_position_id',
        'organization_unit_id',
        'assigned_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditAssignment $assignment): void {
            if (blank($assignment->assigned_at)) {
                $assignment->assigned_at = now();
            }

            $auditorPosition = UserPosition::query()->find($assignment->auditor_position_id);

            if (! $auditorPosition || ! $auditorPosition->is_active) {
                throw new \RuntimeException(
                    'Auditor yang dipilih tidak memiliki posisi yang aktif.'
                );
            }

            $organizationUnit = OrganizationUnit::query()->find($assignment->organization_unit_id);

            if (! $organizationUnit || ! $organizationUnit->is_active) {
                throw new \RuntimeException(
                    'Unit organisasi (auditee) yang dipilih tidak aktif.'
                );
            }

            if ($auditorPosition->organization_unit_id === $assignment->organization_unit_id) {
                throw new \RuntimeException(
                    'Auditor tidak dapat ditugaskan untuk mengaudit unitnya sendiri.'
                );
            }

            $cycle = AuditCycle::query()->find($assignment->audit_cycle_id);

            if ($cycle && in_array($cycle->status, ['completed', 'cancelled'], true)) {
                throw new \RuntimeException(
                    'Tidak dapat menambah penugasan pada siklus audit yang sudah selesai atau dibatalkan.'
                );
            }
        });
    }

    public function auditCycle(): BelongsTo
    {
        return $this->belongsTo(AuditCycle::class);
    }

    public function auditorPosition(): BelongsTo
    {
        return $this->belongsTo(UserPosition::class, 'auditor_position_id');
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }
}
