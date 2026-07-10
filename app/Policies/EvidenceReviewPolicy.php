<?php

namespace App\Policies;

use App\Models\EvidenceReview;
use App\Models\User;

class EvidenceReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('evidence.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('evidence.review');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EvidenceReview $evidenceReview): bool
    {
        if ($evidenceReview->isDecided()) {
            return false;
        }
        
        if ($user->can('evidence.review') && $user->id === $evidenceReview->reviewer_id) {
            return true;
        }

        return $user->can('evidence.approve');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EvidenceReview $evidenceReview): bool
    {
        return ($user->can('evidence.review') || $user->can('evidence.approve'))
            && $evidenceReview->isPending;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function approve(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.approve')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('approved');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function reject(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.reject')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('rejected');
    }

    public function requestRevision(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.review')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('revision_needed');
    }

    public function reopen(User $user, EvidenceReview $evidenceReview): bool
    {
        return ($user->can('evidence.review') || $user->can('evidence.approve'))
            && $evidenceReview->canTransitionTo('pending');
    }
}
