<?php

namespace App\Policies;

use App\Models\EvidenceReview;
use App\Models\User;

class EvidenceReviewPolicy
{
    /**
     * Determine whether the user can view the review queue.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('evidence.review');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.review');
    }

    /**
     * Determine whether the user can update review notes while the
     * linked evidence is still awaiting review.
     */
    public function update(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.review')
            && ! in_array($evidenceReview->status, ['approved', 'rejected'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EvidenceReview $evidenceReview): bool
    {
        return $user->can('evidence.approve')
            && ! in_array($evidenceReview->status, ['approved', 'rejected'], true);
    }
}
