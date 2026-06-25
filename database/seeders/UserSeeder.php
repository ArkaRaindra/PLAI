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
                'name' => 'Ketua LPM',
                'email' => 'ketualpm@example.com',
                'username' => 'ketualpm',
                'password' => 'password',
                'is_active' => true,
                'role' => 'ketua-lpm',
            ],
            [
                'name' => 'Admin Mutu',
                'email' => 'adminmutu@example.com',
                'username' => 'adminmutu',
                'password' => 'password',
                'is_active' => true,
                'role' => 'admin-mutu',
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
                'name' => 'Kaprodi',
                'email' => 'kaprodi@example.com',
                'username' => 'kaprodi',
                'password' => 'password',
                'is_active' => true,
                'role' => 'kaprodi',
            ],
            [
                'name' => 'Sekprodi',
                'email' => 'sekprodi@example.com',
                'username' => 'sekprodi',
                'password' => 'password',
                'is_active' => true,
                'role' => 'sekprodi',
            ],
            [
                'name' => 'Kepala Unit',
                'email' => 'kepalaunit@example.com',
                'username' => 'kepalaunit',
                'password' => 'password',
                'is_active' => true,
                'role' => 'kepala-unit',
            ],
            [
                'name' => 'Dosen',
                'email' => 'dosen@example.com',
                'username' => 'dosen',
                'password' => 'password',
                'is_active' => true,
                'role' => 'dosen',
            ],
            [
                'name' => 'Tendik',
                'email' => 'tendik@example.com',
                'username' => 'tendik',
                'password' => 'password',
                'is_active' => true,
                'role' => 'tendik',
            ],
        ];

        foreach ($data as $item) {
            $user = User::query()->firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'username' => $item['username'],
                    'password' => $item['password'],
                    'is_active' => $item['is_active'],
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ],
            );

            $user->syncRoles([$item['role']]);
        }
    }
}
