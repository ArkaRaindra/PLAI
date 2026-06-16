<?php

namespace Database\Seeders;

use App\Models\Period;
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
        $activePeriod = Period::where('is_active', true)->first();

        $data = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'username' => 'superadmin',
                'password' => 'password',
                'is_active' => true,
                'role' => 'super-admin',
                'faculty' => null,
                'study_program' => null,
                'period_id' => null,
            ],
            [
                'name' => 'Auditor',
                'email' => 'auditor@example.com',
                'username' => 'auditor',
                'password' => 'password',
                'is_active' => true,
                'role' => 'auditor',
                'faculty' => null,
                'study_program' => null,
                'period_id' => $activePeriod?->id,
            ],
            [
                'name' => 'Fakultas',
                'email' => 'fakultas@example.com',
                'username' => 'fakultas',
                'password' => 'password',
                'is_active' => true,
                'role' => 'fakultas',
                'faculty' => 'Fakultas Teknik',
                'study_program' => null,
                'period_id' => $activePeriod?->id,
            ],
            [
                'name' => 'Prodi',
                'email' => 'prodi@example.com',
                'username' => 'prodi',
                'password' => 'password',
                'is_active' => true,
                'role' => 'prodi',
                'faculty' => 'Fakultas Teknik',
                'study_program' => 'Teknik Informatika',
                'period_id' => $activePeriod?->id,
            ],
            [
                'name' => 'Unit Penunjang',
                'email' => 'unitpenunjang@example.com',
                'username' => 'unitpenunjang',
                'password' => 'password',
                'is_active' => true,
                'role' => 'unit-penunjang',
                'faculty' => null,
                'study_program' => null,
                'period_id' => $activePeriod?->id,
            ],
        ];

        foreach ($data as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'username' => $item['username'],
                'password' => $item['password'],
                'is_active' => $item['is_active'],
                'faculty' => $item['faculty'] ?? null,
                'study_program' => $item['study_program'] ?? null,
                'period_id' => $item['period_id'] ?? null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            if (method_exists($user, 'assignRole')) {
            $user->assignRole($item['role']);
            }
        }
    }
}
