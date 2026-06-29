<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first();

        if (! $admin) {
            $admin = User::first();
        }

        $data = [
            [
                'code' => 'KTU_LPM',
                'name' => 'Ketua LPM',
                'description' => 'Kepala Lembaga Penjaminan Mutu',
            ],
            [
                'code' => 'AM_MUTU',
                'name' => 'Admin Mutu',
                'description' => 'Administrator Sistem Penjaminan Mutu',
            ],
            [
                'code' => 'AUD',
                'name' => 'Auditor',
                'description' => 'Auditor internal untuk pemeriksaan akreditasi',
            ],
            [
                'code' => 'KAPRODI',
                'name' => 'Kepala Prodi',
                'description' => 'Kepala Program Studi',
            ],
            [
                'code' => 'SEKPRODI',
                'name' => 'Sekretaris Prodi',
                'description' => 'Sekretaris Program Studi',
            ],
            [
                'code' => 'KPU',
                'name' => 'Kepala Unit',
                'description' => 'Kepala Unit Organisasi',
            ],
            [
                'code' => 'DSN',
                'name' => 'Dosen',
                'description' => 'Dosen Pengajar',
            ],
            [
                'code' => 'TNK',
                'name' => 'Tendik',
                'description' => 'Tenaga Kependidikan',
            ],
        ];

        foreach ($data as $item) {
            Position::query()->firstOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ]
            );
        }
    }
}
