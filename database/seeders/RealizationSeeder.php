<?php

namespace Database\Seeders;

use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class RealizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $targets = Target::all();
        $unit = OrganizationUnit::where('code', 'TI')->firstOrFail();
        $kaprodi = User::where('email', 'kaprodi@example.com')->firstOrFail();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->firstOrFail();

        $statuses = ['draft', 'submitted', 'approved', 'rejected'];

        foreach ($targets->take(6) as $index => $target) {
            $status = $statuses[$index % 4];

            Auth::setUser($admin);

            $realization = Realization::query()->create([
                'target_id' => $target->id,
                'organization_unit_id' => $unit->id,
                'actual_value' => fake()->randomFloat(2, 40, 120),
                'score' => fake()->randomFloat(2, 50, 100),
                'notes' => 'Realisasi capaian untuk ' . $target->indicator->name,
                'status' => $status,
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            if ($status === 'submitted') {
                $realization->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(3),
                ]);
            } elseif ($status === 'approved') {
                $realization->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(5),
                    'approved_by' => (string) $ketuaLpm->id,
                    'approved_at' => now()->subDays(2),
                ]);
            } elseif ($status === 'rejected') {
                $realization->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(4),
                    'rejected_by' => (string) $ketuaLpm->id,
                    'rejected_at' => now()->subDays(1),
                    'note_rejected' => 'Capaian belum memenuhi target yang diharapkan.',
                ]);
            }
        }
    }
}
