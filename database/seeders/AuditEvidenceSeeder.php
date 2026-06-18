<?php

namespace Database\Seeders;

use App\Models\AuditEvidence;
use App\Models\Period;
use App\Models\Standard;
use App\Models\SubStandard;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditEvidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiUser = User::where('username', 'infprodi')->first();
        $subStandard = SubStandard::where('code', 'SUB-001.A')->first();
        $activePeriod = Period::where('is_active', true)->first();
        $standard = Standard::where('code', 'STD-001')->first();

        if ($prodiUser && $subStandard && $activePeriod) {
            $data = [
                [
                    'user_id' => $prodiUser->id,
                    'standard' => $standard->id,
                    'sub_standard_id' => $subStandard->id,
                    'period_id' => $activePeriod->id,
                    'title' => 'Dokumen Sertifikasi Kompetensi Lulusan 2025',
                    'description' => 'Bukti sertifikasi keahlian BNSP mahasiswa prodi informatika.',
                    'file_path' => 'evidences/sertifikasi-2025.pdf',
                    'google_drive_link' => 'https://drive.google.com/drive/folders/sample-link',
                    'status' => 'submitted', 
                    'auditor_note' => null,
                ]
            ];

            foreach ($data as $item) {
                AuditEvidence::create($item);
            }
        }
    }
}
