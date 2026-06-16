<?php

namespace Database\Seeders;

use App\Models\Standard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'STD-001',
                'name' => 'Standar Kompetensi Lulusan',
                'description' => 'Kriteria minimal tentang kualifikasi kemampuan lulusan yang mencakup sikap, pengetahuan, dan keterampilan.',
                'weight' => 5,
            ],
            [
                'code' => 'STD-002',
                'name' => 'Standar Isi Pembelajaran',
                'description' => 'Kriteria minimal tingkat kedalaman dan keluasan materi pembelajaran.',
                'weight' => 4,
            ],
            [
                'code' => 'STD-003',
                'name' => 'Standar Proses Pembelajaran',
                'description' => 'Kriteria minimal tentang pelaksanaan pembelajaran pada program studi.',
                'weight' => 4,
            ],
        ];

        foreach ($data as $item) {
            Standard::create($item);
        }
    }
}
