<?php

namespace Database\Seeders;

use App\Models\Standard;
use App\Models\SubStandard;
use Illuminate\Database\Seeder;

class SubStandardSeeder extends Seeder
{
    public function run(): void
    {
        $std1 = Standard::where('code', 'STD-001')->first();
        $std2 = Standard::where('code', 'STD-002')->first();
        $std3 = Standard::where('code', 'STD-003')->first();

        $data = [
            [
                'standard_id' => $std1?->id,
                'code' => 'SUB-001.A',
                'indicator' => 'Persentase lulusan yang memiliki sertifikasi kompetensi keahlian.',
                'max_score' => 4,
            ],
            [
                'standard_id' => $std1?->id,
                'code' => 'SUB-001.B',
                'indicator' => 'Rata-rata waktu tunggu lulusan untuk mendapatkan pekerjaan pertama (< 6 bulan).',
                'max_score' => 4,
            ],
            [
                'standard_id' => $std2?->id,
                'code' => 'SUB-002.A',
                'indicator' => 'Kesesuaian kurikulum dengan perkembangan IPTEKS dan kebutuhan pengguna lulusan.',
                'max_score' => 4,
            ],
            [
                'standard_id' => $std2?->id,
                'code' => 'SUB-002.B',
                'indicator' => 'Keterlibatan pemangku kepentingan dalam penyusunan kurikulum.',
                'max_score' => 4,
            ],
            [
                'standard_id' => $std3?->id,
                'code' => 'SUB-003.A',
                'indicator' => 'Ketersediaan RPS (Rencana Pembelajaran Semester) yang sesuai standar.',
                'max_score' => 4,
            ],
        ];

        foreach ($data as $item) {
            if (!$item['standard_id']) continue;

            SubStandard::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}