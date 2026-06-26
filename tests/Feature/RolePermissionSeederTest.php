<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_seeder_creates_all_module_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->assertSame(
            count(PermissionSeeder::allPermissionNames()),
            Permission::query()->count(),
        );

        $this->assertDatabaseHas('permissions', [
            'name' => 'master-data.view',
            'guard_name' => PermissionSeeder::GUARD,
        ]);

        $this->assertDatabaseHas('permissions', [
            'name' => 'accreditation.export',
            'guard_name' => PermissionSeeder::GUARD,
        ]);
    }

    public function test_role_seeder_creates_roles_and_syncs_permissions(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $this->assertSame(count(RoleSeeder::ROLES), Role::query()->count());

        $superAdmin = Role::findByName('super-admin', PermissionSeeder::GUARD);
        $this->assertSame(
            count(PermissionSeeder::allPermissionNames()),
            $superAdmin->permissions()->count(),
        );

        $auditor = Role::findByName('auditor', PermissionSeeder::GUARD);
        $this->assertTrue($auditor->hasPermissionTo('audit.verify'));
        $this->assertFalse($auditor->hasPermissionTo('master-data.create'));
    }

    public function test_user_seeder_syncs_each_user_to_expected_role(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        $this->assertTrue(User::query()->where('email', 'ketualpm@example.com')->first()?->hasRole('ketua-lpm'));
        $this->assertTrue(User::query()->where('email', 'dosen@example.com')->first()?->hasRole('dosen'));
        $this->assertTrue(User::query()->where('email', 'tendik@example.com')->first()?->hasRole('tendik'));
    }
}
