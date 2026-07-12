<?php

namespace Database\Seeders;

use App\Enums\CalculationMethod;
use App\Models\Indicator;
use App\Models\StandardVersion;
use App\Models\User;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $versions = StandardVersion::all();

        if ($versions->isEmpty()) {
            return;
        }

        $indicatorDefs = [
            [
                'code' => 'IND-001',
                'name' => 'Tata Pamong',
                'description' => 'Indikator tata pamong dan tata kelola',
                'calculation_method' => CalculationMethod::Count,
                'measurement_unit' => 'unit',
                'weight' => 15.00,
            ],
            [
                'code' => 'IND-002',
                'name' => 'Sistem Jaminan Mutu',
                'description' => 'Indikator sistem jaminan mutu internal',
                'calculation_method' => CalculationMethod::Count,
                'measurement_unit' => 'unit',
                'weight' => 20.00,
            ],
            [
                'code' => 'IND-003',
                'name' => 'Pembelajaran',
                'description' => 'Indikator proses pembelajaran',
                'calculation_method' => CalculationMethod::Percentage,
                'measurement_unit' => 'persen',
                'weight' => 25.00,
            ],
            [
                'code' => 'IND-004',
                'name' => 'Lulusan',
                'description' => 'Indikator capaian lulusan',
                'calculation_method' => CalculationMethod::Percentage,
                'measurement_unit' => 'persen',
                'weight' => 20.00,
            ],
            [
                'code' => 'IND-005',
                'name' => 'Penelitian dan Pengabdian',
                'description' => 'Indikator penelitian dan pengabdian kepada masyarakat',
                'calculation_method' => CalculationMethod::Sum,
                'measurement_unit' => 'kegiatan',
                'weight' => 10.00,
            ],
            [
                'code' => 'IND-006',
                'name' => 'Kerja Sama',
                'description' => 'Indikator kerja sama dengan pihak eksternal',
                'calculation_method' => CalculationMethod::Count,
                'measurement_unit' => 'paket',
                'weight' => 10.00,
            ],
        ];

        foreach ($versions as $version) {
            foreach ($indicatorDefs as $index => $def) {
                Indicator::query()->firstOrCreate(
                    [
                        'standard_version_id' => $version->id,
                        'code' => $def['code'].'-V'.$version->version,
                    ],
                    array_merge($def, [
                        'standard_version_id' => $version->id,
                        'created_by' => (string) $admin->id,
                        'updated_by' => (string) $admin->id,
                    ])
                );
            }
        }
    }
}
