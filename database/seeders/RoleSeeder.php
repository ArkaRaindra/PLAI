<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            [
                'name' => 'super-admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'auditor',
                'guard_name' => 'web',
            ],
            [
                'name' => 'fakultas',
                'guard_name' => 'web',
            ],
            [
                'name' => 'prodi',
                'guard_name' => 'web',
            ],
            [
                'name' => 'unit-penunjang',
                'guard_name' => 'web',
            ],
        ];

        foreach ($data as $item) {
            Role::create($item);
        }
    }
}
