<?php

namespace App\Policies;

use App\Models\SelfAssessment;
use App\Models\User;

class SelfAssessmentPolicy
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
    public function view(User $user, SelfAssessment $selfAssessment): bool
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
    public function update(User $user, SelfAssessment $selfAssessment): bool
    {
        return $user->can('ppepp.update')
            && in_array($selfAssessment->status, ['draft', 'rejected'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SelfAssessment $selfAssessment): bool
    {
        return $user->can('ppepp.delete') && $selfAssessment->status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function submit(User $user, SelfAssessment $selfAssessment): bool
    {
        return $user->can('ppepp.update')
            && $selfAssessment->canTransitionTo('submitted')
            && $selfAssessment->details()->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function approve(User $user, SelfAssessment $selfAssessment): bool
    {
        return $user->can('ppepp.approve') && $selfAssessment->canTransitionTo('approved');
    }

    public function reject(User $user, SelfAssessment $selfAssessment): bool
    {
        return $user->can('ppepp.approve') && $selfAssessment->canTransitionTo('rejected');
    }
}
