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
            'review' => $this->markReviewStatus($evidence, 'review'),
            'approved' => $this->resolveReview($evidence, 'approved', new EvidenceApprovedNotification($evidence)),
            'rejected' => $this->resolveReview(
                $evidence,
                'rejected',
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
        EvidenceReview::query()->updateOrCreate(['evidence_id' => $evidence->id], ['status' => 'pending']);

        $this->notifyReviewers($evidence);
    }

    /**
     * Keep the review task's own status column in sync with the evidence's
     * workflow status. Without this, EvidenceReview.status stays "pending"
     * forever after creation — even once the evidence has actually been
     * approved/rejected — since nothing else in the app writes to it.
     */
    private function markReviewStatus(Evidences $evidence, string $status): void
    {
        EvidenceReview::query()
            ->where('evidence_id', $evidence->id)
            ->update(['status' => $status]);
    }

    private function resolveReview(Evidences $evidence, string $status, NotificationClass $notification): void
    {
        EvidenceReview::query()
            ->where('evidence_id', $evidence->id)
            ->update(['status' => $status, 'reviewed_at' => now()]);

        $this->notifyOwner($evidence, $notification);
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