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
            [
                'code' => 'STD-004',
                'name' => 'Standar Penilaian Pembelajaran',
                'description' => 'Kriteria minimal tentang penilaian hasil belajar mahasiswa.',
                'weight' => 4,
            ],
            [
                'code' => 'STD-005',
                'name' => 'Standar Dosen dan Tenaga Kependidikan',
                'description' => 'Kriteria minimal kualifikasi dan kompetensi dosen serta tenaga kependidikan.',
                'weight' => 5,
            ],
            [
                'code' => 'STD-006',
                'name' => 'Standar Sarana dan Prasarana',
                'description' => 'Kriteria minimal tentang fasilitas pembelajaran dan penunjang.',
                'weight' => 3,
            ],
            [
                'code' => 'STD-007',
                'name' => 'Standar Pengelolaan',
                'description' => 'Kriteria minimal tentang perencanaan, pelaksanaan, dan pengawasan kegiatan pendidikan.',
                'weight' => 4,
            ],
            [
                'code' => 'STD-008',
                'name' => 'Standar Pembiayaan',
                'description' => 'Kriteria minimal tentang komponen dan besaran biaya penyelenggaraan pendidikan.',
                'weight' => 3,
            ],
        ];

        foreach ($data as $item) {
            Standard::create($item);
        }
    }
}
