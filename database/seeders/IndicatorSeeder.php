<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\Standard;
use App\Models\StandardVersion;
use App\Models\User;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $stdVersion = StandardVersion::where('standard_id', Standard::where('code', 'STD-001')->value('id'))->firstOrFail();

        $indicators = [
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-001',
                'name' => 'Tata Pamong',
                'description' => 'Indikator tata pamong dan tata kelola',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 15.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-002',
                'name' => 'Sistem Jaminan Mutu',
                'description' => 'Indikator sistem jaminan mutu internal',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 20.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-003',
                'name' => 'Pembelajaran',
                'description' => 'Indikator proses pembelajaran',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 25.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-004',
                'name' => 'Lulusan',
                'description' => 'Indikator capaian lulusan',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 20.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-005',
                'name' => 'Penelitian dan Pengabdian',
                'description' => 'Indikator penelitian dan pengabdian kepada masyarakat',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 10.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-006',
                'name' => 'Kerja Sama',
                'description' => 'Indikator kerja sama dengan pihak eksternal',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 10.00,
            ],
            [
                'standard_version_id' => $stdVersion->id,
                'code' => 'IND-006',
                'name' => 'Kerja Sama',
                'description' => 'Indikator kerja sama dengan pihak eksternal',
                'calculation_method' => 'count',
                'measurement_unit' => 'unit',
                'weight' => 10.00,
            ],
        ];

        foreach ($indicators as $indicator) {
            Indicator::query()->firstOrCreate(
                ['code' => $indicator['code']],
                array_merge($indicator, [
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ])
            );
        }
    }
}
