<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'auditor', 'guard_name' => 'web'],
            ['name' => 'fakultas', 'guard_name' => 'web'],
            ['name' => 'prodi', 'guard_name' => 'web'],
            ['name' => 'unit-penunjang', 'guard_name' => 'web'],
        ];

        foreach ($data as $item) {
            Role::firstOrCreate(
                ['name' => $item['name'], 'guard_name' => $item['guard_name']],
                $item
            );
        }
    }
}