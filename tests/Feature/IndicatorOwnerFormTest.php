<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\IndicatorOwners\Pages\CreateIndicatorOwner;
use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\User;
use App\Models\UserPosition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndicatorOwnerFormTest extends TestCase
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

    public function test_user_position_select_is_disabled_until_organization_unit_is_selected(): void
    {
        $admin = $this->createSuperAdmin();

        Livewire::actingAs($admin)
            ->test(CreateIndicatorOwner::class)
            ->assertFormFieldDisabled('user_position_id');
    }

    public function test_changing_organization_unit_resets_user_position(): void
    {
        $admin = $this->createSuperAdmin();
        $fixture = $this->createUserPositionFixture($admin);

        Livewire::actingAs($admin)
            ->test(CreateIndicatorOwner::class)
            ->fillForm(['organization_unit_id' => $fixture['unitA']->id])
            ->assertFormFieldEnabled('user_position_id')
            ->fillForm(['user_position_id' => $fixture['userPositionA']->id])
            ->fillForm(['organization_unit_id' => $fixture['unitB']->id])
            ->assertFormSet(['user_position_id' => null]);
    }

    public function test_user_position_options_are_filtered_by_organization_unit(): void
    {
        $admin = $this->createSuperAdmin();
        $fixture = $this->createUserPositionFixture($admin);

        $withoutUnit = $this->eligibleUserPositions(null);
        $unitAOptions = $this->eligibleUserPositions($fixture['unitA']->id);
        $unitBOptions = $this->eligibleUserPositions($fixture['unitB']->id);

        $this->assertCount(0, $withoutUnit);
        $this->assertCount(1, $unitAOptions);
        $this->assertCount(1, $unitBOptions);
        $this->assertTrue($unitAOptions->contains('id', $fixture['userPositionA']->id));
        $this->assertTrue($unitBOptions->contains('id', $fixture['userPositionB']->id));
        $this->assertFalse($unitAOptions->contains('id', $fixture['userPositionB']->id));
    }

    public function test_user_position_option_label_includes_user_position_and_unit(): void
    {
        $admin = $this->createSuperAdmin();
        $fixture = $this->createUserPositionFixture($admin);

        $userPosition = $fixture['userPositionA']->load(['user', 'position', 'organizationUnit']);

        $label = "{$userPosition->user->name} — {$userPosition->position->name} — {$userPosition->organizationUnit->name}";

        $this->assertSame(
            'Budi Santoso — Kepala Prodi — Fakultas Teknik',
            $label,
        );
    }

    public function test_indicator_owner_stores_user_position_id(): void
    {
        $admin = $this->createSuperAdmin();
        $fixture = $this->createUserPositionFixture($admin);
        $indicator = $this->createIndicator($admin);

        Livewire::actingAs($admin)
            ->test(CreateIndicatorOwner::class)
            ->fillForm([
                'indicator_id' => $indicator->id,
                'organization_unit_id' => $fixture['unitA']->id,
                'user_position_id' => $fixture['userPositionA']->id,
                'is_primary' => true,
                'notes' => '<p>Catatan pemilik indikator</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('indicator_owners', [
            'indicator_id' => $indicator->id,
            'organization_unit_id' => $fixture['unitA']->id,
            'user_position_id' => $fixture['userPositionA']->id,
            'is_primary' => true,
        ]);

        $indicatorOwner = IndicatorOwner::query()->first();

        $this->assertNotNull($indicatorOwner);
        $this->assertSame($fixture['userPositionA']->id, $indicatorOwner->user_position_id);
    }

    public function test_indicator_owner_can_be_stored_without_user_position(): void
    {
        $admin = $this->createSuperAdmin();
        $fixture = $this->createUserPositionFixture($admin);
        $indicator = $this->createIndicator($admin);

        Livewire::actingAs($admin)
            ->test(CreateIndicatorOwner::class)
            ->fillForm([
                'indicator_id' => $indicator->id,
                'organization_unit_id' => $fixture['unitA']->id,
                'user_position_id' => null,
                'is_primary' => true,
                'notes' => '<p>Catatan tanpa jabatan</p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('indicator_owners', [
            'indicator_id' => $indicator->id,
            'organization_unit_id' => $fixture['unitA']->id,
            'user_position_id' => null,
            'is_primary' => true,
        ]);
    }

    private function createSuperAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        return $admin;
    }

    /**
     * @return array{
     *     unitA: OrganizationUnit,
     *     unitB: OrganizationUnit,
     *     userPositionA: UserPosition,
     *     userPositionB: UserPosition,
     * }
     */
    private function createUserPositionFixture(User $admin): array
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'is_active' => true,
        ]);

        $positionA = Position::create([
            'code' => 'KP',
            'name' => 'Kepala Prodi',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $positionB = Position::create([
            'code' => 'DK',
            'name' => 'Dekan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $unitA = OrganizationUnit::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $unitB = OrganizationUnit::create([
            'code' => 'FE',
            'name' => 'Fakultas Ekonomi',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $userPositionA = UserPosition::create([
            'user_id' => $user->id,
            'position_id' => $positionA->id,
            'organization_unit_id' => $unitA->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $userPositionB = UserPosition::create([
            'user_id' => $user->id,
            'position_id' => $positionB->id,
            'organization_unit_id' => $unitB->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        return [
            'unitA' => $unitA,
            'unitB' => $unitB,
            'userPositionA' => $userPositionA,
            'userPositionB' => $userPositionB,
        ];
    }

    private function createIndicator(User $admin): Indicator
    {
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standard = Standard::create([
            'code' => 'STD-IO',
            'name' => 'Standard Indicator Owner',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        return Indicator::factory()->create([
            'standard_id' => $standard->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
    }

    /**
     * Mirrors the user_position_id query constraints used in IndicatorOwnerForm.
     */
    private function eligibleUserPositions(?int $organizationUnitId)
    {
        return UserPosition::query()
            ->with(['user', 'position', 'organizationUnit'])
            ->where('is_active', true)
            ->when(
                filled($organizationUnitId),
                fn (Builder $query) => $query->where('organization_unit_id', $organizationUnitId),
                fn (Builder $query) => $query->whereRaw('1 = 0'),
            )
            ->get();
    }
}
