<?php

namespace Database\Seeders;

use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationUnitSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();

        $units = [
            [
                'code' => 'POLITEKNIK',
                'name' => 'Politeknik Budi Mulia',
                'type' => 'POLITEKNIK',
                'is_active' => true,
            ],
            [
                'code' => 'FT',
                'name' => 'Fakultas Teknik',
                'type' => 'JURUSAN',
                'is_active' => true,
            ],
            [
                'code' => 'TI',
                'name' => 'Teknik Informatika',
                'type' => 'PROGRAM STUDI',
                'is_active' => true,
            ],
            [
                'code' => 'TM',
                'name' => 'Teknik Mesin',
                'type' => 'PROGRAM STUDI',
                'is_active' => true,
            ],
            [
                'code' => 'UPM',
                'name' => 'Unit Penjaminan Mutu',
                'type' => 'UPM',
                'is_active' => true,
            ],
            [
                'code' => 'P3M',
                'name' => 'Pusat Penelitian dan Pengabdian Masyarakat',
                'type' => 'P3M',
                'is_active' => true,
            ],
            [
                'code' => 'SPI',
                'name' => 'Satuan Pengelola Informasi',
                'type' => 'SPI',
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            OrganizationUnit::query()->firstOrCreate(
                ['code' => $unit['code']],
                array_merge($unit, [
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ])
            );
        }
    }
}
