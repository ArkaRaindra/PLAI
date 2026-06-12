<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage users',
            'manage standards',
            'manage periods',
            'view all scores',
            'input evidence',
            'validate evidence',
            'self assessment',
            'export reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->givePermissionTo(['view dashboard', 'view all scores', 'validate evidence', 'export reports']);

        $prodi = Role::firstOrCreate(['name' => 'prodi', 'guard_name' => 'web']);
        $prodi->givePermissionTo(['input evidence', 'self assessment']);

        $fakultas = Role::firstOrCreate(['name' => 'fakultas', 'guard_name' => 'web']);
        $fakultas->givePermissionTo(['view dashboard', 'view all scores', 'export reports']);
    }
}