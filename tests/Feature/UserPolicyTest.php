<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
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

    public function test_super_admin_can_manage_users(): void
    {
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super-admin');

        $targetUser = User::factory()->create(['is_active' => true]);

        $this->assertTrue($superAdmin->can('viewAny', User::class));
        $this->assertTrue($superAdmin->can('view', $targetUser));
        $this->assertTrue($superAdmin->can('create', User::class));
        $this->assertTrue($superAdmin->can('update', $targetUser));
        $this->assertTrue($superAdmin->can('delete', $targetUser));
    }

    public function test_non_super_admin_cannot_manage_users(): void
    {
        $auditor = User::factory()->create(['is_active' => true]);
        $auditor->assignRole('auditor');

        $targetUser = User::factory()->create(['is_active' => true]);

        $this->assertTrue($auditor->can('viewAny', User::class));
        $this->assertFalse($auditor->can('view', $targetUser));
        $this->assertFalse($auditor->can('create', User::class));
        $this->assertFalse($auditor->can('update', $targetUser));
        $this->assertFalse($auditor->can('delete', $targetUser));
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get('/super-admin')
            ->assertOk();
    }
}
