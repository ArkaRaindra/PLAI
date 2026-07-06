<?php

namespace Tests\Feature;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealizationWorkflowTest extends TestCase
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

    public function test_draft_can_be_submitted(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $realization = $this->makeRealization($kaprodi, 'draft');

        $this->actingAs($kaprodi);
        $realization->update(['status' => 'submitted']);

        $this->assertSame('submitted', $realization->fresh()->status);
        $this->assertSame($kaprodi->id, $realization->fresh()->submitted_by);
    }

    public function test_draft_cannot_jump_directly_to_approved(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $realization = $this->makeRealization($kaprodi, 'draft');

        $this->actingAs($kaprodi);

        $this->expectException(\RuntimeException::class);

        $realization->update(['status' => 'approved']);
    }

    public function test_submitted_can_be_approved_by_ketua_lpm(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $realization = $this->makeRealization($kaprodi, 'submitted');

        $this->actingAs($ketuaLpm);
        $this->assertTrue($ketuaLpm->can('approve', $realization));

        $realization->update(['status' => 'approved']);

        $this->assertSame('approved', $realization->fresh()->status);
        $this->assertSame($ketuaLpm->id, $realization->fresh()->approved_by);
    }

    public function test_kaprodi_cannot_approve_own_realization(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $realization = $this->makeRealization($kaprodi, 'submitted');

        $this->assertFalse($kaprodi->can('approve', $realization));
    }

    public function test_submitted_can_be_rejected_with_note(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $realization = $this->makeRealization($kaprodi, 'submitted');

        $this->actingAs($ketuaLpm);
        $realization->update([
            'status' => 'rejected',
            'note_rejected' => 'Data pendukung belum lengkap',
        ]);

        $realization->refresh();

        $this->assertSame('rejected', $realization->status);
        $this->assertSame($ketuaLpm->id, $realization->rejected_by);
        $this->assertSame('Data pendukung belum lengkap', $realization->note_rejected);
    }

    public function test_rejected_can_be_resubmitted(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $realization = $this->makeRealization($kaprodi, 'rejected');

        $this->actingAs($kaprodi);
        $realization->update(['status' => 'submitted']);

        $this->assertSame('submitted', $realization->fresh()->status);
    }

    public function test_approved_is_a_terminal_status(): void
    {
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $realization = $this->makeRealization($ketuaLpm, 'approved');

        $this->actingAs($ketuaLpm);

        $this->expectException(\RuntimeException::class);

        $realization->update(['status' => 'submitted']);
    }

    public function test_status_history_is_recorded_for_every_transition(): void
    {
        $kaprodi = $this->makeUser('kaprodi');
        $ketuaLpm = $this->makeUser('ketua-lpm');
        $realization = $this->makeRealization($kaprodi, 'draft');

        $this->actingAs($kaprodi);
        $realization->update(['status' => 'submitted']);

        $this->actingAs($ketuaLpm);
        $realization->update(['status' => 'approved']);

        $statuses = $realization->statusHistories()->latest('id')->pluck('status')->all();

        $this->assertSame(['approved', 'submitted', 'draft'], $statuses);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    private function makeRealization(User $creator, string $status): Realization
    {
        $this->actingAs($creator);

        $indicator = Indicator::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        $qualityPeriod = QualityPeriod::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $qualityPeriod->id,
            'target_value' => 100,
            'created_by' => (string) $creator->id,
        ]);

        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $creator->id,
        ]);

        return Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => $status,
            'created_by' => (string) $creator->id,
        ]);
    }
}