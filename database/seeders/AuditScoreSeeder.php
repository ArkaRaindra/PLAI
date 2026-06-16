<?php

namespace Database\Seeders;

use App\Models\AuditScore;
use App\Models\Period;
use App\Models\SubStandard;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiUser = User::where('username', 'infprodi')->first();
        $auditorUser = User::where('username', 'auditor')->first();
        $subStandard = SubStandard::where('code', 'SUB-001.A')->first();
        $activePeriod = Period::where('is_active', true)->first();

        if ($prodiUser && $auditorUser && $subStandard && $activePeriod) {
            $data = [
                [
                    'user_id' => $prodiUser->id,
                    'sub_standard_id' => $subStandard->id,
                    'period_id' => $activePeriod->id,
                    'auditor_id' => $auditorUser->id,
                    'score' => 4, // Skor maksimal berdasarkan max_score sub_standard
                    'comment' => 'Dokumen lengkap dan mencakup target kelulusan prodi.',
                ]
            ];

            foreach ($data as $item) {
                AuditScore::create($item);
            }
        }
    }
}
