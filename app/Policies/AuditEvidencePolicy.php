<?php

namespace App\Policies;

use App\Models\AuditEvidence;
use App\Models\User;

class AuditEvidencePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['prodi', 'auditor', 'fakultas']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuditEvidence $evidence): bool
    {
        if ($user->hasRole('prodi')) {
            return $user->id === $evidence->user_id;
        }

        return $user->hasRole(['auditor', 'fakultas']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('prodi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuditEvidence $evidence): bool
    {
        if ($user->hasRole('prodi')) {
            return $user->id === $evidence->user_id && $evidence->status === 'draft';
        }
        if ($user->hasRole('auditor')) {
            return $evidence->status === 'submitted';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuditEvidence $evidence): bool
    {
        return $user->hasRole('prodi') && $user->id === $evidence->user_id && $evidence->status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AuditEvidence $auditEvidence): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AuditEvidence $auditEvidence): bool
    {
        return false;
    }
}
