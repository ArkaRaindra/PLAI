<?php

namespace App\Policies;

use App\Models\AuditChecklistTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuditChecklistTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('audit.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $user->can('audit.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('audit.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $user->can('audit.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuditChecklistTemplate $auditChecklistTemplate): bool
    {
        return $user->can('audit.delete');
    }
}
