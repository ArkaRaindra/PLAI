<?php

namespace Database\Seeders;

use App\Models\StandardSource;
use App\Models\User;
use Illuminate\Database\Seeder;

class StandardSourceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();

        $sources = [
            [
                'name' => 'IKU Politeknik Budi Mulia',
                'code' => 'SRC-IKU',
                'description' => 'Indikator Kinerja Utama politeknik',
                'is_active' => true,
                'is_external' => false,
            ],
            [
                'name' => 'BAN-PT',
                'code' => 'SRC-BANPT',
                'description' => 'Badan Akreditasi Nasional Perguruan Tinggi',
                'is_active' => true,
                'is_external' => true,
            ],
            [
                'name' => 'ISO 9001:2015',
                'code' => 'SRC-ISO',
                'description' => 'Standar sistem manajemen mutu',
                'is_active' => true,
                'is_external' => true,
            ],
            [
                'name' => 'Permenristekdikti',
                'code' => 'SRC-Permen',
                'description' => 'Peraturan Menteri Riset Teknologi dan Pendidikan Tinggi',
                'is_active' => true,
                'is_external' => true,
            ],
        ];

        foreach ($sources as $source) {
            StandardSource::query()->firstOrCreate(
                ['code' => $source['code']],
                array_merge($source, [
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ])
            );
        }
    }
}
