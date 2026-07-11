<?php

namespace App\Policies;

use App\Models\EvidenceLinks;
use App\Models\User;

class EvidenceLinksPolicy
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
    public function view(User $user, EvidenceLinks $evidenceLinks): bool
    {
        return $user->can('evidence.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('evidence.upload') || $user->can('evidence.review');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EvidenceLinks $evidenceLinks): bool
    {
        if ($user->can('evidence.review')) {
            return true;
        }

        return $user->can('evidence.upload') && (int) $evidenceLinks->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EvidenceLinks $evidenceLinks): bool
    {
        return $this->update($user, $evidenceLinks);
    }
}