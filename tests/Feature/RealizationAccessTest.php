<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class RealizationAccessTest extends TestCase
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

    public function test_realization_list_redirects_without_target_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->get('/super-admin/realizations')
            ->assertRedirect(TargetResource::getUrl('index'));
    }

    public function test_realization_list_is_scoped_to_target(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$targetA, $unitA] = $this->createTargetWithUnit($admin, 'Unit Alpha');
        [$targetB, $unitB] = $this->createTargetWithUnit($admin, 'Unit Beta');

        Realization::query()->create([
            'target_id' => $targetA->id,
            'organization_unit_id' => $unitA->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        Realization::query()->create([
            'target_id' => $targetB->id,
            'organization_unit_id' => $unitB->id,
            'actual_value' => 20,
            'score' => 15,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(RealizationResource::getListUrl($targetA->id))
            ->assertOk()
            ->assertSee('Unit Alpha')
            ->assertDontSee('Unit Beta');
    }

    public function test_create_realization_page_redirects_without_target_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->get('/super-admin/realizations/create')
            ->assertRedirect(TargetResource::getUrl('index'));
    }

    public function test_create_realization_page_is_accessible_with_target_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$target] = $this->createTargetWithUnit($admin);

        $this->actingAs($admin)
            ->get(RealizationResource::getCreateUrl($target->id))
            ->assertOk();
    }

    public function test_target_list_shows_realization_link(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$target] = $this->createTargetWithUnit($admin);

        $this->actingAs($admin)
            ->get(TargetResource::getUrl('index'))
            ->assertOk()
            ->assertSeeHtml(RealizationResource::getListUrl($target->id));
    }

    public function test_duplicate_organization_unit_per_target_is_rejected(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$target, $unit] = $this->createTargetWithUnit($admin);

        Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        $validator = Validator::make(
            [
                'target_id' => $target->id,
                'organization_unit_id' => $unit->id,
            ],
            [
                'organization_unit_id' => [
                    Rule::unique('realizations', 'organization_unit_id')
                        ->where(fn ($query) => $query->where('target_id', $target->id)),
                ],
            ],
        );

        $this->assertFalse($validator->passes());
    }

    public function test_target_can_have_multiple_realizations_with_different_units(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$target, $unitA] = $this->createTargetWithUnit($admin);

        $unitB = OrganizationUnit::query()->create([
            'code' => 'UNIT-B',
            'name' => 'Unit B',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unitA->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unitB->id,
            'actual_value' => 12,
            'score' => 9,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        $this->assertSame(2, Realization::query()->where('target_id', $target->id)->count());
    }

    public function test_submitted_status_sets_submitted_by_on_create(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        [$target, $unit] = $this->createTargetWithUnit($admin);

        $this->actingAs($admin);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'submitted',
            'created_by' => (string) $admin->id,
        ]);

        $realization->refresh();

        $this->assertSame($admin->id, $realization->submitted_by);
        $this->assertNotNull($realization->submitted_at);
        $this->assertNull($realization->approved_by);
    }

    public function test_status_transition_sets_actor_on_update(): void
    {
        $submitter = User::factory()->create(['is_active' => true, 'name' => 'Submitter']);
        $approver = User::factory()->create(['is_active' => true, 'name' => 'Approver']);
        [$target, $unit] = $this->createTargetWithUnit($submitter);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'draft',
            'created_by' => (string) $submitter->id,
        ]);

        $this->actingAs($submitter);
        $realization->update(['status' => 'submitted']);
        $realization->refresh();

        $this->assertSame($submitter->id, $realization->submitted_by);

        $this->actingAs($approver);
        $realization->update(['status' => 'approved']);
        $realization->refresh();

        $this->assertSame($approver->id, $realization->approved_by);
        $this->assertNotNull($realization->approved_at);
        $this->assertSame($submitter->id, $realization->submitted_by);
    }

    public function test_rejected_status_sets_rejected_by(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        [$target, $unit] = $this->createTargetWithUnit($admin);

        $this->actingAs($admin);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'rejected',
            'created_by' => (string) $admin->id,
        ]);

        $realization->refresh();

        $this->assertSame($admin->id, $realization->rejected_by);
        $this->assertNotNull($realization->rejected_at);
    }

    public function test_saving_without_status_change_does_not_overwrite_submitted_by(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $other = User::factory()->create(['is_active' => true]);
        [$target, $unit] = $this->createTargetWithUnit($admin);

        $this->actingAs($admin);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => 10,
            'score' => 8,
            'status' => 'submitted',
            'created_by' => (string) $admin->id,
        ]);

        $originalSubmittedAt = $realization->submitted_at;

        $this->actingAs($other);
        $realization->update(['actual_value' => 11]);
        $realization->refresh();

        $this->assertSame($admin->id, $realization->submitted_by);
        $this->assertTrue($originalSubmittedAt->equalTo($realization->submitted_at));
    }

    /**
     * @return array{0: Target, 1: OrganizationUnit}
     */
    private function createTargetWithUnit(User $admin, ?string $unitName = null): array
    {
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $qualityPeriod = QualityPeriod::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $qualityPeriod->id,
            'target_value' => 100,
            'created_by' => (string) $admin->id,
        ]);

        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => $unitName ?? 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        return [$target, $unit];
    }
}
