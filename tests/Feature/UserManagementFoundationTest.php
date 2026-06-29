<?php

namespace Tests\Feature;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use App\Models\UserPosition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_assigned_role_position_active_status_and_reset_password(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('super-admin');

        $user = User::factory()->create([
            'is_active' => true,
            'password' => 'old-password',
        ]);
        $user->assignRole('auditor');

        $position = Position::create([
            'code' => 'AUD',
            'name' => 'Auditor',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $organizationUnit = OrganizationUnit::create([
            'code' => 'UPM',
            'name' => 'Unit Penjaminan Mutu',
            'type' => 'UPM',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $userPosition = UserPosition::create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'organization_unit_id' => $organizationUnit->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $user->forceFill([
            'password' => 'new-password-123',
        ])->save();

        $this->assertTrue($user->hasRole('auditor'));
        $this->assertTrue($user->fresh()->is_active);
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
        $this->assertModelExists($userPosition);
        $this->assertTrue($user->positions()->whereKey($position->id)->exists());
    }
}
