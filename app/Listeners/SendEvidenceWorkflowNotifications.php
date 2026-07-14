<?php

namespace App\Listeners;

use App\Events\WorkflowTransitioned;
use App\Models\EvidenceReview;
use App\Models\Evidences;
use App\Models\User;
use App\Notifications\EvidenceApprovedNotification;
use App\Notifications\EvidenceRejectedNotification;
use App\Notifications\EvidenceSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as NotificationClass;
use Illuminate\Support\Facades\Notification;

class SendEvidenceWorkflowNotifications implements ShouldQueue
{
    public function handle(WorkflowTransitioned $event): void
    {
        $workflow = $event->workflowInstance;

        if ($workflow->entity_type !== 'evidence') {
            return;
        }

        $evidence = $workflow->entity;

        if (! $evidence instanceof Evidences) {
            return;
        }

        match ($event->toStatus) {
            'submitted' => $this->openReview($evidence),
            'approved' => $this->notifyOwner($evidence, new EvidenceApprovedNotification($evidence)),
            'rejected' => $this->notifyOwner(
                $evidence,
                new EvidenceRejectedNotification($evidence, $workflow->histories()->orderByDesc('id')->first()?->notes),
            ),
            default => null,
        };
    }

    /**
     * Create the review task (so the evidence appears in the reviewer
     * queue) and notify all reviewers.
     */
    private function openReview(Evidences $evidence): void
    {
        EvidenceReview::query()->updateOrCreate(['evidence_id' => $evidence->id]);

        $this->notifyReviewers($evidence);
    }

    private function notifyReviewers(Evidences $evidence): void
    {
        $reviewers = User::query()->permission('evidence.review')->get();

        if ($reviewers->isEmpty()) {
            return;
        }

        Notification::send($reviewers, new EvidenceSubmittedNotification($evidence));
    }

    private function notifyOwner(Evidences $evidence, NotificationClass $notification): void
    {
        $owner = User::query()->find($evidence->created_by);

        $owner?->notify($notification);
    }
}
