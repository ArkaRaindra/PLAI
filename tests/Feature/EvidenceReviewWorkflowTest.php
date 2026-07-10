<?php

namespace Tests\Feature;

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

    public function test_admin_mutu_can_assign_a_reviewer(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $auditor = $this->makeUser('auditor');
        $evidence = $this->makeEvidence($adminMutu);

        $this->actingAs($adminMutu);

        $this->assertTrue($adminMutu->can('create', EvidenceReview::class));

        $review = EvidenceReview::query()->create([
            'evidence_id' => $evidence->id,
            'reviewer_id' => $auditor->id,
            'created_by' => (string) $adminMutu->id,
        ]);

        $this->assertSame('pending', $review->status);
        $this->assertSame($adminMutu->id, $review->assigned_by);
        $this->assertNotNull($review->assigned_at);
    }

    public function test_dosen_cannot_assign_a_reviewer(): void
    {
        $dosen = $this->makeUser('dosen');

        $this->assertFalse($dosen->can('create', EvidenceReview::class));
    }

    public function test_assigned_reviewer_can_approve_with_permission(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $adminMutu, $adminMutu);

        $this->actingAs($adminMutu);
        $this->assertTrue($adminMutu->can('approve', $review));

        $review->update([
            'status' => 'approved',
            'review_notes' => 'Dokumen lengkap dan sesuai standar.',
        ]);

        $review->refresh();
        $this->assertSame('approved', $review->status);
        $this->assertNotNull($review->reviewed_at);
    }

    public function test_auditor_without_approve_permission_cannot_approve(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $auditor = $this->makeUser('auditor');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $auditor, $adminMutu);

        $this->assertFalse($auditor->can('approve', $review));
    }

    public function test_auditor_can_request_revision_with_notes(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $auditor = $this->makeUser('auditor');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $auditor, $adminMutu);

        $this->actingAs($auditor);
        $this->assertTrue($auditor->can('requestRevision', $review));

        $review->update([
            'status' => 'revision_needed',
            'review_notes' => 'Mohon lampirkan dokumen pendukung versi terbaru.',
        ]);

        $review->refresh();
        $this->assertSame('revision_needed', $review->status);
        $this->assertSame('Mohon lampirkan dokumen pendukung versi terbaru.', $review->review_notes);
    }

    public function test_someone_who_is_not_the_assigned_reviewer_cannot_review(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $anotherAdminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $adminMutu, $adminMutu);

        $this->assertFalse($anotherAdminMutu->can('approve', $review));
        $this->assertFalse($anotherAdminMutu->can('reject', $review));
    }

    public function test_pending_cannot_jump_directly_to_pending_noop_or_skip_rules(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $adminMutu, $adminMutu, 'approved');

        $this->actingAs($adminMutu);

        $this->expectException(\RuntimeException::class);

        $review->update(['status' => 'pending']);
    }

    public function test_revision_needed_can_be_reopened_to_pending(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $adminMutu, $adminMutu, 'revision_needed');

        $this->actingAs($adminMutu);
        $this->assertTrue($adminMutu->can('reopen', $review));

        $review->update(['status' => 'pending']);

        $review->refresh();
        $this->assertSame('pending', $review->status);
        $this->assertNull($review->reviewed_at);
    }

    public function test_approved_is_a_terminal_status_and_cannot_be_edited_further(): void
    {
        $adminMutu = $this->makeUser('admin-mutu');
        $evidence = $this->makeEvidence($adminMutu);
        $review = $this->makeReview($evidence, $adminMutu, $adminMutu, 'approved');

        $this->actingAs($adminMutu);

        $this->assertFalse($adminMutu->can('update', $review));
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

    private function makeReview(
        Evidences $evidence,
        User $reviewer,
        User $assignedBy,
        string $status = 'pending',
    ): EvidenceReview {
        $this->actingAs($assignedBy);

        return EvidenceReview::query()->create([
            'evidence_id' => $evidence->id,
            'reviewer_id' => $reviewer->id,
            'status' => $status,
            'created_by' => (string) $assignedBy->id,
        ]);
    }
}