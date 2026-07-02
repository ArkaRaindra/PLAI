<?php

namespace Tests\Feature\Filament\UserPositions;

use App\Filament\SuperAdmin\Resources\UserPositions\Pages\EditUserPosition;
use App\Filament\SuperAdmin\Resources\UserPositions\UserPositionResource;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use App\Models\UserPosition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditUserPositionTest extends TestCase
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

    public function test_edit_page_shows_inactive_user_name_instead_of_id(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $inactiveUser = User::factory()->create([
            'name' => 'Inactive Position User',
            'is_active' => false,
        ]);

        $position = Position::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'OU-TEST',
            'name' => 'Unit Organisasi Test',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
            'updated_by' => (string) $admin->id,
        ]);

        $userPosition = UserPosition::query()->create([
            'user_id' => $inactiveUser->id,
            'position_id' => $position->id,
            'organization_unit_id' => $organizationUnit->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
            'created_by' => (string) $admin->id,
            'updated_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(UserPositionResource::getUrl('edit', ['record' => $userPosition]))
            ->assertOk()
            ->assertSee('Inactive Position User');

        Livewire::actingAs($admin)
            ->test(EditUserPosition::class, ['record' => $userPosition->getKey()])
            ->assertFormSet([
                'user_id' => $inactiveUser->id,
            ])
            ->assertSee('Inactive Position User');
    }
}
