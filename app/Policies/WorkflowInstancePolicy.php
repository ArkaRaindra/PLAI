<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowInstance;
use Illuminate\Auth\Access\Response;

class WorkflowInstancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('evidnece.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('evidence.submit') || $user->can('evidence.upload');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkflowInstance $workflowInstance): bool
    {
        return in_array($workflowInstance->current_status, ['draft', 'rejected'], true)
            && ($user->can('evidence.submit') || $user->can('evidence.upload'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.upload') && $workflowInstance->current_status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function submit(User $user, WorkflowInstance $workflowInstance): bool
    {
        return ($user->can('evidence.submit') || $user->can('evidence.upload'))
            && $workflowInstance->canTransitionTo('submitted');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function review(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.review')
            && $workflowInstance->canTransitionTo('review');
    }

    public function approve(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.approve')
            && $workflowInstance->canTransitionTo('approved');
    }

    public function reject(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.reject')
            && $workflowInstance->canTransitionTo('reject');
    }

    public function publish(User $user, WorkflowInstance $workflowInstance): bool
    {
        return $user->can('evidence.publish')
            && $workflowInstance->canTransitionTo('published');
    }

     public function reopen(User $user, WorkflowInstance $workflowInstance): bool
    {
        return ($user->can('evidence.submit') || $user->can('evidence.upload'))
            && $workflowInstance->canTransitionTo('draft');
    }
}
