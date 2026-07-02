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
