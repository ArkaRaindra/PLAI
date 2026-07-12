<?php

namespace Database\Seeders;

use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class SelfAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $kaprodi = User::where('email', 'kaprodi@example.com')->first() ?? User::where('role', 'kaprodi')->first() ?? User::first();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->first() ?? User::where('role', 'ketua-lpm')->first() ?? User::first();

        $units = OrganizationUnit::whereIn('type', ['PROGRAM STUDI', 'UPM', 'P3M'])->get();
        $activePeriod = QualityPeriod::where('status', 'active')->first();

        if ($units->isEmpty() || ! $activePeriod) {
            return;
        }

        $statuses = ['draft', 'submitted', 'approved', 'rejected'];

        foreach ($units->take(4) as $index => $unit) {
            $status = $statuses[$index] ?? 'draft';

            $selfAssessment = SelfAssessment::firstOrCreate(
                [
                    'organization_unit_id' => $unit->id,
                    'quality_period_id' => $activePeriod->id,
                ],
                [
                    'status' => 'draft',
                    'summary' => 'Self assessment untuk periode '.$activePeriod->name.' - '.$unit->name,
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );

            $approvedRealizations = Realization::where('organization_unit_id', $unit->id)
                ->where('status', 'approved')
                ->with('target.indicator')
                ->get();

            if ($approvedRealizations->isEmpty()) {
                continue;
            }

            foreach ($approvedRealizations->take(3) as $realization) {
                $indicator = $realization->target->indicator;

                SelfAssessmentDetail::firstOrCreate(
                    [
                        'self_assessment_id' => $selfAssessment->id,
                        'realization_id' => $realization->id,
                    ],
                    [
                        'score' => $realization->score,
                        'analysis' => 'Analisis capaian indikator '.$indicator->name,
                        'strength' => 'Kekuatan: Tim yang solid',
                        'weakness' => 'Kelemahan: Kurangnya dokumentasi',
                        'created_by' => (string) $admin->id,
                        'updated_by' => (string) $admin->id,
                    ]
                );
            }

            if ($selfAssessment->wasRecentlyCreated) {
                Auth::setUser($kaprodi);

                if ($status === 'submitted') {
                    $selfAssessment->update([
                        'status' => 'submitted',
                        'submitted_by' => (string) $kaprodi->id,
                        'submitted_at' => now()->subDays(3),
                    ]);
                } elseif ($status === 'approved') {
                    $selfAssessment->update([
                        'status' => 'submitted',
                        'submitted_by' => (string) $kaprodi->id,
                        'submitted_at' => now()->subDays(5),
                    ]);

                    Auth::setUser($ketuaLpm);
                    $selfAssessment->update([
                        'status' => 'approved',
                        'approved_by' => (string) $ketuaLpm->id,
                        'approved_at' => now()->subDays(2),
                    ]);
                } elseif ($status === 'rejected') {
                    $selfAssessment->update([
                        'status' => 'submitted',
                        'submitted_by' => (string) $kaprodi->id,
                        'submitted_at' => now()->subDays(4),
                    ]);

                    Auth::setUser($ketuaLpm);
                    $selfAssessment->update([
                        'status' => 'rejected',
                        'rejected_by' => (string) $ketuaLpm->id,
                        'rejected_at' => now()->subDays(1),
                        'note_rejected' => 'Analisis capaian belum lengkap',
                    ]);
                }

                Auth::setUser($admin);
            }
        }
    }
}
