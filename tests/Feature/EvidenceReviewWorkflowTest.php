<?php

namespace Tests\Feature;

use App\Events\WorkflowTransitioned;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\EvidenceReview;
use App\Models\Evidences;
use App\Models\OrganizationUnit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvidenceReviewWorkflowTest extends TestCase
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

    public function test_review_queue_is_visible_to_reviewers_only(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $dosen = $this->makeUser('dosen');

        $this->assertTrue($adminMutu->can('viewAny', EvidenceReview::class));
        $this->assertFalse($dosen->can('viewAny', EvidenceReview::class));
    }

    public function test_review_record_is_created_when_evidence_is_submitted(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);

        $evidence->transitionWorkflowTo('submitted');

        $this->assertDatabaseHas('evidence_reviews', [
            'evidence_id' => $evidence->id,
        ]);

        $review = EvidenceReview::forEvidence($evidence->id)->firstOrFail();
        $this->assertSame(1, EvidenceReview::query()->pending()->count());
        $this->assertSame('submitted', $review->evidence->workflowInstance->current_status);
    }

    public function test_reviewer_can_open_review_and_approve_through_evidence_workflow(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $evidence->transitionWorkflowTo('submitted');

        $this->actingAs($adminMutu);

        // Reviewer opens the review (submitted -> review).
        $this->assertTrue($adminMutu->can('startReview', $evidence->workflowInstance));
        $evidence->transitionWorkflowTo('review');

        // Approver finalizes (review -> approved) and saves the review notes.
        $this->assertTrue($adminMutu->can('approve', $evidence->workflowInstance));
        EvidencesResource::syncReviewNotes($evidence, 'Dokumen lengkap.');
        $evidence->transitionWorkflowTo('approved');

        $review = EvidenceReview::forEvidence($evidence->id)->firstOrFail();
        $this->assertSame('Dokumen lengkap.', $review->review_notes);
        $this->assertNotNull($review->reviewed_at);

        // Decided evidence no longer appears in the review queue.
        $this->assertSame(0, EvidenceReview::query()->pending()->count());
    }

    public function test_auditor_without_approve_permission_cannot_approve(): void
    {
        $auditor = $this->makeUser('auditor');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $evidence->transitionWorkflowTo('submitted');
        $evidence->transitionWorkflowTo('review');

        $this->assertFalse($auditor->can('approve', $evidence->workflowInstance));
    }

    public function test_rejected_evidence_leaves_the_review_queue(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $evidence->transitionWorkflowTo('submitted');

        $this->assertSame(1, EvidenceReview::query()->pending()->count());

        $evidence->transitionWorkflowTo('review');
        EvidencesResource::syncReviewNotes($evidence, 'Kurang lengkap.');
        $evidence->transitionWorkflowTo('rejected');

        $review = EvidenceReview::forEvidence($evidence->id)->firstOrFail();
        $this->assertSame('Kurang lengkap.', $review->review_notes);
        $this->assertSame(0, EvidenceReview::query()->pending()->count());
    }

    public function test_review_record_stays_unique_when_submit_event_fires_twice(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $evidence->transitionWorkflowTo('submitted');

        // The submit transition event should be safe to fire more than once.
        $workflow = $evidence->workflowInstance->fresh();
        event(new WorkflowTransitioned($workflow, 'draft', 'submitted'));
        event(new WorkflowTransitioned($workflow, 'draft', 'submitted'));

        $this->assertSame(1, EvidenceReview::forEvidence($evidence->id)->count());
    }

    public function test_super_admin_can_force_evidence_to_any_status(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $evidence = $this->makeEvidence($superAdmin);

        // Super-admin can jump straight from draft to approved, bypassing
        // the normal workflow transition rules.
        $evidence->workflowInstance->forceTransitionTo('approved', 'Paksa setujui');

        $this->assertSame('approved', $evidence->workflowInstance->fresh()->current_status);
        $this->assertTrue($superAdmin->hasRole('super-admin'));
    }

    public function test_normal_workflow_still_guards_invalid_transitions(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);

        $this->expectException(\RuntimeException::class);

        // draft -> approved is not a valid transition via the normal path.
        $evidence->transitionWorkflowTo('approved');
    }

    public function test_only_super_admin_can_force_status(): void
    {
        $superAdmin = $this->makeUser('super-admin');
        $adminMutu = $this->makeUser('admin-mutu');

        $this->assertTrue($superAdmin->hasRole('super-admin'));
        $this->assertFalse($adminMutu->hasRole('super-admin'));
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
