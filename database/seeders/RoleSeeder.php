<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    public const ROLES = [
        'super-admin',
        'ketua-lpm',
        'admin-mutu',
        'auditor',
        'kaprodi',
        'sekprodi',
        'kepala-unit',
        'dosen',
        'tendik',
    ];

    /**
     * @var array<string, list<string>>
     */
    public const ROLE_PERMISSIONS = [
        'super-admin' => ['*'],
        'ketua-lpm' => [
            'master-data.view',
            'ppepp.view',
            'ppepp.approve',
            'evidence.view',
            'evidence.approve',
            'evidence.publish',
            'audit.view',
            'audit.verify',
            'audit.close',
            'capa.view',
            'capa.verify',
            'capa.close',
            'rtm.view',
            'rtm.approve',
            'risk.view',
            'risk.approve',
            'accreditation.view',
            'accreditation.export',
        ],
        'admin-mutu' => [
            'master-data.*',
            'ppepp.*',
            'evidence.view',
            'evidence.review',
            'evidence.approve',
            'evidence.reject',
            'evidence.publish',
            'audit.view',
            'audit.create',
            'audit.update',
            'capa.view',
            'capa.create',
            'capa.update',
            'capa.verify',
            'rtm.*',
            'risk.*',
            'accreditation.*',
        ],
        'auditor' => [
            'master-data.view',
            'ppepp.view',
            'evidence.view',
            'evidence.review',
            'audit.*',
            'capa.view',
            'capa.verify',
        ],
        'kaprodi' => [
            'master-data.view',
            'ppepp.view',
            'ppepp.create',
            'ppepp.update',
            'evidence.view',
            'evidence.upload',
            'capa.view',
            'capa.create',
            'capa.update',
            'rtm.view',
            'rtm.create',
            'rtm.update',
            'risk.view',
            'risk.create',
            'risk.update',
        ],
        'sekprodi' => [
            'master-data.view',
            'ppepp.view',
            'ppepp.create',
            'ppepp.update',
            'evidence.view',
            'evidence.upload',
            'capa.view',
            'capa.create',
            'capa.update',
            'rtm.view',
        ],
        'kepala-unit' => [
            'master-data.view',
            'ppepp.view',
            'ppepp.create',
            'ppepp.update',
            'evidence.view',
            'evidence.upload',
            'capa.view',
            'capa.create',
            'capa.update',
            'rtm.view',
            'rtm.create',
            'rtm.update',
        ],
        'dosen' => [
            'master-data.view',
            'ppepp.view',
            'evidence.view',
            'evidence.upload',
        ],
        'tendik' => [
            'master-data.view',
            'ppepp.view',
            'evidence.view',
            'evidence.upload',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::ROLES as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => PermissionSeeder::GUARD,
            ]);

            $permissionNames = PermissionSeeder::resolvePermissionNames(
                self::ROLE_PERMISSIONS[$roleName] ?? [],
            );

            $permissions = Permission::query()
                ->whereIn('name', $permissionNames)
                ->where('guard_name', PermissionSeeder::GUARD)
                ->get();

            $role->syncPermissions($permissions);
        }
    }
}
