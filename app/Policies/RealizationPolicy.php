<?php

namespace App\Policies;

use App\Models\Realization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RealizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ppepp.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       return $user->can('ppepp.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.update')
            && in_array($realization->status, ['draft', 'rejected'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.delete') && $realization->status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function submit(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.update') && $realization->canTransitionTo('submitted');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function approve(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.approve') && $realization->canTransitionTo('approved');
    }

    public function reject(User $user, Realization $realization): bool
    {
        return $user->can('ppepp.approve') && $realization->canTransitionTo('rejected');
    }
}
