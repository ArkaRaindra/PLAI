<?php

namespace App\Policies;

use App\Models\Evidences;
use App\Models\User;
use App\Models\WorkflowInstance;

class WorkflowInstancePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkflowInstance $workflowInstance): bool
    {
        return match ($workflowInstance->entity_type) {
            Evidences::class => $user->can('evidence.view'),
            default => false,
        };
    }

    /**
     * draft -> submitted.
     */
    public function submit(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $workflowInstance->canTransitionTo('submitted')) {
            return false;
        }

        return match ($workflowInstance->entity_type) {
            Evidences::class => $this->isEvidenceOwnerOrManager($user, $workflowInstance),
            default => false,
        };
    }

    /**
     * submitted -> review.
     */
    public function startReview(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $workflowInstance->canTransitionTo('review')) {
            return false;
        }

        return match ($workflowInstance->entity_type) {
            Evidences::class => $user->can('evidence.review'),
            default => false,
        };
    }

    /**
     * review -> approved.
     */
    public function approve(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $workflowInstance->canTransitionTo('approved')) {
            return false;
        }

        return match ($workflowInstance->entity_type) {
            Evidences::class => $user->can('evidence.approve'),
            default => false,
        };
    }

    /**
     * review -> rejected.
     */
    public function reject(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $workflowInstance->canTransitionTo('rejected')) {
            return false;
        }

        return match ($workflowInstance->entity_type) {
            Evidences::class => $user->can('evidence.reject'),
            default => false,
        };
    }

    /**
     * approved -> published.
     */
    public function publish(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $workflowInstance->canTransitionTo('published')) {
            return false;
        }

        return match ($workflowInstance->entity_type) {
            Evidences::class => $user->can('evidence.publish'),
            default => false,
        };
    }

    private function isEvidenceOwnerOrManager(User $user, WorkflowInstance $workflowInstance): bool
    {
        if (! $user->can('evidence.upload') && ! $user->can('evidence.review')) {
            return false;
        }

        if ($user->can('evidence.review')) {
            return true;
        }

        /** @var Evidences|null $evidence */
        $evidence = $workflowInstance->entity;

        return $evidence !== null && (int) $evidence->created_by === $user->id;
    }
}