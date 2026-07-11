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
     * Determine whether the user can create models (assign a reviewer).
     */
    public function create(User $user): bool
    {
        return $user->can('evidence.review');
    }

    /**
     * Determine whether the user can update the model (reassign reviewer /
     * write review notes). Status transitions are authorized separately
     * below (approve/reject/requestRevision/reopen).
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
            && $evidenceReview->isPending();
    }

    /**
     * Determine whether the user can mark the review as approved.
     */
    public function approve(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.approve')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('approved');
    }

    /**
     * Determine whether the user can mark the review as rejected.
     */
    public function reject(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.reject')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('rejected');
    }

    /**
     * Determine whether the user can request a revision from the uploader.
     */
    public function requestRevision(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.review')
            && $user->id === $evidenceReview->reviewer_id
            && $evidenceReview->canTransitionTo('revision_needed');
    }

    /**
     * Determine whether the user can reopen a review (revision_needed -> pending).
     */
    public function reopen(User $user, EvidenceReview $evidenceReview): bool
    {
        return ($user->can('evidence.review') || $user->can('evidence.approve'))
            && $evidenceReview->canTransitionTo('pending');
    }
}