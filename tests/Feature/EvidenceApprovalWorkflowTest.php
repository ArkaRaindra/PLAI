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

    public function test_evidence_gets_a_draft_workflow_instance_on_creation(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->assertNotNull($evidence->workflowInstance);
        $this->assertSame('draft', $evidence->workflowInstance->current_status);
        $this->assertCount(1, $evidence->workflowInstance->histories);
    }

    public function test_owner_can_submit_their_own_draft_evidence(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->actingAs($dosen);

        $this->assertTrue($dosen->can('submit', $evidence->workflowInstance));

        $evidence->transitionWorkflowTo('submitted');

        $this->assertSame('submitted', $evidence->workflowInstance->fresh()->current_status);
    }

    public function test_other_users_cannot_submit_someone_elses_draft_evidence(): void
    {
        $dosen = $this->makeUser('dosen');
        $anotherDosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen);

        $this->assertFalse($anotherDosen->can('submit', $evidence->workflowInstance));
    }

    public function test_admin_mutu_can_start_review_and_approve(): void
    {
        $dosen = $this->makeUser('dosen');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen, 'submitted');

        $this->actingAs($adminMutu);

        $this->assertTrue($adminMutu->can('startReview', $evidence->workflowInstance));
        $evidence->transitionWorkflowTo('review');
        $this->assertSame('review', $evidence->workflowInstance->fresh()->current_status);

        $this->assertTrue($adminMutu->can('approve', $evidence->workflowInstance->fresh()));
        $evidence->workflowInstance->fresh()->transitionTo('approved', 'Lengkap dan sesuai.');
        $this->assertSame('approved', $evidence->workflowInstance->fresh()->current_status);
    }

    public function test_auditor_cannot_approve_but_admin_mutu_or_ketua_lpm_can_publish(): void
    {
        $dosen = $this->makeUser('dosen');
        $auditor = $this->makeUser('auditor');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $evidence = $this->makeEvidence($dosen, 'approved');

        $this->assertFalse($auditor->can('publish', $evidence->workflowInstance));
        $this->assertTrue($ketuaLpm->can('publish', $evidence->workflowInstance));

        $this->actingAs($ketuaLpm);
        $evidence->transitionWorkflowTo('published');

        $this->assertSame('published', $evidence->workflowInstance->fresh()->current_status);
    }

    public function test_rejected_evidence_returns_to_draft_and_history_accumulates(): void
    {
        $dosen = $this->makeUser('dosen');
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($dosen, 'review');

        $this->actingAs($adminMutu);

        $this->assertTrue($adminMutu->can('reject', $evidence->workflowInstance));
        $evidence->workflowInstance->transitionTo('rejected', 'Dokumen belum lengkap.');

        $instance = $evidence->workflowInstance->fresh();
        $this->assertSame('rejected', $instance->current_status);

        $this->assertFalse($adminMutu->can('submit', $instance)); // rejected state, not draft yet
        $instance->transitionTo('draft', 'Menunggu revisi dari pengunggah.');
        $this->assertSame('draft', $instance->fresh()->current_status);

        // draft -> submitted -> review -> rejected -> draft = 4 transitions + 1 initial = 5 history rows
        $this->assertSame(5, $instance->histories()->count());
    }

    public function test_owner_can_revise_a_rejected_evidence_back_to_draft(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen, 'rejected');

        $instance = $evidence->workflowInstance->fresh();

        $this->assertTrue($dosen->can('revise', $instance));
        $this->assertFalse($dosen->can('submit', $instance)); // must revise first

        $instance->transitionTo('draft', 'Telah direvisi sesuai catatan.');

        $instance = $instance->fresh();
        $this->assertSame('draft', $instance->current_status);
        $this->assertTrue($dosen->can('submit', $instance));
    }

    public function test_unrelated_user_cannot_revise_someone_elses_rejected_evidence(): void
    {
        $dosen = $this->makeUser('dosen');
        $anotherDosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen, 'rejected');

        $this->assertFalse($anotherDosen->can('revise', $evidence->workflowInstance->fresh()));
    }

    public function test_publish_cannot_be_skipped_from_review(): void
    {
        $dosen = $this->makeUser('dosen');
        $evidence = $this->makeEvidence($dosen, 'review');

        $this->expectException(\RuntimeException::class);

        $evidence->workflowInstance->transitionTo('published');
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Create an evidence and optionally fast-forward its workflow to a
     * given status by walking through the legal transition chain.
     */
    private function makeEvidence(User $owner, string $targetStatus = 'draft'): Evidences
    {
        $this->actingAs($owner);

        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $owner->id,
        ]);

        $evidence = Evidences::query()->create([
            'organization_unit_id' => $unit->id,
            'title' => 'Bukti '.fake()->words(3, true),
            'description' => fake()->sentence(),
            'created_by' => (string) $owner->id,
        ]);

        $chain = ['draft', 'submitted', 'review', 'approved', 'published'];
        $targetIndex = array_search($targetStatus, $chain, true);

        if ($targetIndex === false) {
            // e.g. 'rejected' is reached from 'review'
            $this->fastForward($evidence->workflowInstance, ['submitted', 'review']);
            $evidence->workflowInstance->fresh()->transitionTo($targetStatus);

            return $evidence->fresh();
        }

        $this->fastForward($evidence->workflowInstance, array_slice($chain, 1, $targetIndex));

        return $evidence->fresh();
    }

    /**
     * @param  list<string>  $steps
     */
    private function fastForward(WorkflowInstance $instance, array $steps): void
    {
        foreach ($steps as $step) {
            $instance->fresh()->transitionTo($step);
        }
    }
}
