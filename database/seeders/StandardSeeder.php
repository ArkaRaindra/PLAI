<?php

namespace Database\Seeders;

use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\User;
use Illuminate\Database\Seeder;

class StandardSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $ikuSource = StandardSource::where('code', 'SRC-IKU')->firstOrFail();
        $banptSource = StandardSource::where('code', 'SRC-BANPT')->firstOrFail();
        $isoSource = StandardSource::where('code', 'SRC-ISO')->firstOrFail();

        $standards = [
            [
                'code' => 'STD-001',
                'name' => 'Tata Pamong, Tata Kelola, dan Sistem Jaminan Mutu',
                'description' => 'Standar tata pamong, tata kelola, dan sistem jaminan mutu internal politeknik',
                'standard_source_id' => $ikuSource->id,
                'is_active' => true,
            ],
            [
                'code' => 'STD-002',
                'name' => 'Mahasiswa',
                'description' => 'Standar mahasiswa dan proses pembelajaran',
                'standard_source_id' => $ikuSource->id,
                'is_active' => true,
            ],
            [
                'code' => 'STD-003',
                'name' => 'Sumber Daya Manusia',
                'description' => 'Standar sumber daya manusia di politeknik',
                'standard_source_id' => $ikuSource->id,
                'is_active' => true,
            ],
            [
                'code' => 'STD-004',
                'name' => 'Kriteria IKU 1-9',
                'description' => 'Kriteria akreditasi BAN-PT untuk institusi politeknik',
                'standard_source_id' => $banptSource->id,
                'is_active' => true,
            ],
            [
                'code' => 'STD-005',
                'name' => 'Sistem Manajemen Mutu ISO 9001',
                'description' => 'Persyaratan sistem manajemen mutu ISO 9001:2015',
                'standard_source_id' => $isoSource->id,
                'is_active' => true,
            ],
        ];

        foreach ($standards as $standard) {
            Standard::query()->firstOrCreate(
                ['code' => $standard['code']],
                array_merge($standard, [
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ])
            );
        }
    }
}
