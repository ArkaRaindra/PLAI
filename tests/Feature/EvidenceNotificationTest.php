<?php

namespace Tests\Feature;

use App\Models\Evidences;
use App\Models\Notification as AppNotification;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Notifications\EvidenceApprovedNotification;
use App\Notifications\EvidenceRejectedNotification;
use App\Notifications\EvidenceSubmittedNotification;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EvidenceNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_reviewers_are_notified_when_evidence_is_submitted(): void
    {
        Notification::fake();

        $dosen = $this->makeUser('dosen');
        $auditor = $this->makeUser('auditor');
        $evidence = $this->makeEvidence($dosen);

        $evidence->workflowInstance->transitionTo('submitted');

        Notification::assertSentTo($auditor, EvidenceSubmittedNotification::class);
    }

    public function test_owner_is_notified_when_evidence_is_approved(): void
    {
        Notification::fake();

        $dosen = $this->makeUser('dosen');
        $auditor = $this->makeUser('auditor');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen);

        $evidence->workflowInstance->transitionTo('submitted');
        $evidence->workflowInstance->transitionTo('review');
        $evidence->workflowInstance->transitionTo('approved');

        Notification::assertSentTo($dosen, EvidenceApprovedNotification::class);
    }

    public function test_owner_is_notified_with_the_reason_when_evidence_is_rejected(): void
    {
        Notification::fake();

        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $evidence->workflowInstance->transitionTo('submitted');
        $evidence->workflowInstance->transitionTo('review');
        $evidence->workflowInstance->transitionTo('rejected', 'Dokumen belum lengkap.');

        Notification::assertSentTo(
            $dosen,
            EvidenceRejectedNotification::class,
            fn (EvidenceRejectedNotification $notification): bool => $notification->reason === 'Dokumen belum lengkap.',
        );
    }

    public function test_in_app_channel_persists_a_notification_row(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $dosen->notify(new EvidenceApprovedNotification($evidence));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $dosen->id,
            'title' => 'Evidence Disetujui',
            'is_read' => false,
        ]);
    }

    public function test_unread_scope_and_mark_as_read(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $dosen->notify(new EvidenceApprovedNotification($evidence));

        $notification = AppNotification::query()->where('user_id', $dosen->id)->first();

        $this->assertSame(1, AppNotification::query()->unread()->count());

        $notification->markAsRead();

        $this->assertSame(0, AppNotification::query()->unread()->count());
    }

    public function test_no_reviewers_means_no_exception_is_thrown(): void
    {
        Notification::fake();

        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        // No auditor/admin-mutu user exists yet, so there's no one with
        // evidence.review permission. This must not throw.
        $evidence->workflowInstance->transitionTo('submitted');

        $this->assertSame('submitted', $evidence->workflowInstance->fresh()->current_status);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function makeEvidence(User $creator): Evidences
    {
        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $creator->id,
        ]);

        return Evidences::query()->create([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti '.fake()->words(3, true),
            'description' => fake()->sentence(),
            'created_by' => (string) $creator->id,
        ]);
    }
}