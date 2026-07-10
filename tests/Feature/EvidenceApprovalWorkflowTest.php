<?php

namespace Tests\Feature;

use App\Models\Evidences;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\WorkflowInstance;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvidenceApprovalWorkflowTest extends TestCase
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

    public function test_a_new_workflow_instance_starts_as_draft_with_a_history_entry(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);

        $workflow = WorkflowInstance::openFor($evidence);

        $this->assertSame('draft', $workflow->current_status);
        $this->assertSame(1, $workflow->histories()->count());
        $this->assertSame('created', $workflow->histories()->first()->action);
    }

    public function test_full_happy_path_from_draft_to_published_records_full_history(): void
    {
        $dosen = $this->makeUser('dosen');
        $auditor = $this->makeUser('auditor');
        $adminMutu = $this->makeUser('admin-mutu');
        $ketuaLpm = $this->makeUser('ketua-lpm');

        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);
        $workflow = WorkflowInstance::openFor($evidence);

        $this->assertTrue($dosen->can('submit', $workflow));
        $workflow->update(['current_status' => 'submitted']);
        $workflow->refresh();
        $this->assertSame('submitted', $workflow->current_status);
        $this->assertSame($dosen->id, $workflow->submitted_by);
        $this->assertNotNull($workflow->submitted_at);

        $this->actingAs($auditor);
        $this->assertTrue($auditor->can('review', $workflow));
        $workflow->update(['current_status' => 'review']);
        $workflow->refresh();
        $this->assertSame('review', $workflow->current_status);
        $this->assertSame($auditor->id, $workflow->reviewed_by);

        $this->actingAs($adminMutu);
        $this->assertTrue($adminMutu->can('approve', $workflow));
        $workflow->update(['current_status' => 'approved']);
        $workflow->refresh();
        $this->assertSame('approved', $workflow->current_status);
        $this->assertSame($adminMutu->id, $workflow->approved_by);

        $this->assertTrue($ketuaLpm->can('publish', $workflow));
        $this->actingAs($ketuaLpm);
        $workflow->update(['current_status' => 'published']);
        $workflow->refresh();
        $this->assertSame('published', $workflow->current_status);
        $this->assertSame($ketuaLpm->id, $workflow->published_by);
        $this->assertNotNull($workflow->published_at);

        // created + submitted + review + approved + published
        $this->assertSame(5, $workflow->histories()->count());

        $statuses = $workflow->histories()->orderBy('id')->pluck('status')->all();
        $this->assertSame(['draft', 'submitted', 'review', 'approved', 'published'], $statuses);
    }

    public function test_rejected_evidence_can_be_reopened_and_resubmitted(): void
    {
        $dosen = $this->makeUser('dosen');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);
        $workflow = WorkflowInstance::openFor($evidence);
        $workflow->update(['current_status' => 'submitted']);

        $this->actingAs($adminMutu);
        $this->assertTrue($adminMutu->can('reject', $workflow));
        $workflow->update([
            'current_status' => 'rejected',
            'rejection_reason' => 'Dokumen belum lengkap.',
        ]);
        $workflow->refresh();

        $this->assertSame('rejected', $workflow->current_status);
        $this->assertSame('Dokumen belum lengkap.', $workflow->rejection_reason);

        $lastHistory = $workflow->histories()->first();
        $this->assertSame('rejected', $lastHistory->status);
        $this->assertSame('Dokumen belum lengkap.', $lastHistory->notes);

        $this->actingAs($dosen);
        $this->assertTrue($dosen->can('reopen', $workflow));
        $workflow->update(['current_status' => 'draft']);
        $workflow->refresh();

        $this->assertSame('draft', $workflow->current_status);
    }

    public function test_invalid_transitions_are_rejected(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);
        $workflow = WorkflowInstance::openFor($evidence);

        $this->expectException(\RuntimeException::class);

        $workflow->update(['current_status' => 'approved']);
    }

    public function test_published_is_a_terminal_status(): void
    {
        $dosen = $this->makeUser('dosen');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);
        $workflow = WorkflowInstance::openFor($evidence);
        $workflow->update(['current_status' => 'submitted']);
        $workflow->update(['current_status' => 'review']);

        $this->actingAs($adminMutu);
        $workflow->update(['current_status' => 'approved']);
        $workflow->update(['current_status' => 'published']);
        $workflow->refresh();

        $this->assertFalse($workflow->canTransitionTo('draft'));
        $this->assertFalse($workflow->canTransitionTo('approved'));
        $this->assertSame([], WorkflowInstance::TRANSITIONS['published']);
    }

    public function test_dosen_without_review_permission_cannot_move_to_review(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);
        $workflow = WorkflowInstance::openFor($evidence);
        $workflow->update(['current_status' => 'submitted']);

        $this->assertFalse($dosen->can('review', $workflow));
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