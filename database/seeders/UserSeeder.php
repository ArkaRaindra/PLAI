<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'username' => 'superadmin',
                'password' => 'password',
                'is_active' => true,
                'role' => 'super-admin',
            ],
            [
                'name' => 'Auditor',
                'email' => 'auditor@example.com',
                'username' => 'auditor',
                'password' => 'password',
                'is_active' => true,
                'role' => 'auditor',
            ],
            [
                'name' => 'Fakultas',
                'email' => 'fakultas@example.com',
                'username' => 'fakultas',
                'password' => 'password',
                'is_active' => true,
                'role' => 'fakultas',
            ],
            [
                'name' => 'Prodi',
                'email' => 'prodi@example.com',
                'username' => 'prodi',
                'password' => 'password',
                'is_active' => true,
                'role' => 'prodi',
            ],
            [
                'name' => 'Unit Penunjang',
                'email' => 'unitpenunjang@example.com',
                'username' => 'unitpenunjang',
                'password' => 'password',
                'is_active' => true,
                'role' => 'unit-penunjang',
            ],
        ];

        foreach ($data as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'username' => $item['username'],
                'password' => $item['password'],
                'is_active' => $item['is_active'],
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            $user->assignRole($item['role']);
        }
    }
}
