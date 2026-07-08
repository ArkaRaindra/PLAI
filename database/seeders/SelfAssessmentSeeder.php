<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentDetail;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Seeder;

class SelfAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $units = OrganizationUnit::whereIn('code', ['TI', 'TM', 'UPM', 'P3M'])->get();
        $period = QualityPeriod::where('status', 'active')->firstOrFail();
        $kaprodi = User::where('email', 'kaprodi@example.com')->firstOrFail();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->firstOrFail();

        $statuses = ['draft', 'submitted', 'approved', 'rejected'];

        foreach ($statuses as $index => $status) {
            $unit = $units->get($index);

            $selfAssessment = SelfAssessment::query()->create([
                'organization_unit_id' => $unit->id,
                'quality_period_id' => $period->id,
                'status' => $status,
                'summary' => 'Self assessment untuk periode '.$period->name.' - '.$unit->name,
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            $indicator = Indicator::factory()->create([
                'code' => 'IND-'.fake()->unique()->numerify('###'),
                'name' => 'Indikator '.fake()->words(3, true),
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            $target = Target::query()->create([
                'indicator_id' => $indicator->id,
                'quality_period_id' => $period->id,
                'target_value' => 100,
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            $realization = Realization::query()->create([
                'target_id' => $target->id,
                'organization_unit_id' => $unit->id,
                'actual_value' => 10,
                'score' => fake()->randomFloat(2, 60, 95),
                'status' => 'approved',
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            SelfAssessmentDetail::query()->create([
                'self_assessment_id' => $selfAssessment->id,
                'realization_id' => $realization->id,
                'score' => $realization->score,
                'analysis' => 'Analisis capaian indikator',
                'strength' => 'Kekuatan:Tim yang solid',
                'weakness' => 'Kelemahan: Kurangnya dokumentasi',
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            if ($status === 'submitted') {
                $selfAssessment->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(3),
                ]);
            } elseif ($status === 'approved') {
                $selfAssessment->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(5),
                    'approved_by' => (string) $ketuaLpm->id,
                    'approved_at' => now()->subDays(2),
                ]);
            } elseif ($status === 'rejected') {
                $selfAssessment->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(4),
                    'rejected_by' => (string) $ketuaLpm->id,
                    'rejected_at' => now()->subDays(1),
                    'note_rejected' => 'Analisis capaian belum lengkap',
                ]);
            }
        }
    }
}
