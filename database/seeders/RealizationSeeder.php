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
        $targets = Target::orderBy('id')->get();
        $unit = OrganizationUnit::where('code', 'TI')->firstOrFail();
        $kaprodi = User::where('email', 'kaprodi@example.com')->firstOrFail();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->firstOrFail();

        $scenarios = [
            ['status' => 'approved', 'achievement' => 'over'],
            ['status' => 'submitted', 'achievement' => 'random'],
            ['status' => 'approved', 'achievement' => 'under'],
            ['status' => 'rejected', 'achievement' => 'random'],
            ['status' => 'draft', 'achievement' => 'random'],
            ['status' => 'submitted', 'achievement' => 'over'],
        ];

        foreach ($targets as $index => $target) {
            $scenario = $scenarios[$index % count($scenarios)];

            Auth::setUser($admin);

            $actualValue = match ($scenario['achievement']) {
                'over' => (float) $target->target_value + fake()->randomFloat(2, 5, 25),
                'under' => max(1, (float) $target->target_value - fake()->randomFloat(2, 5, 25)),
                default => fake()->randomFloat(2, 30, 140),
            };

            $realization = Realization::query()->create([
                'target_id' => $target->id,
                'organization_unit_id' => $unit->id,
                'actual_value' => $actualValue,
                'score' => fake()->randomFloat(2, 50, 100),
                'notes' => 'Realisasi capaian untuk ' . $target->indicator->name . ' (' . $unit->name . ')',
                'status' => $scenario['status'],
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            if ($scenario['status'] === 'submitted') {
                $realization->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(3),
                ]);
            } elseif ($scenario['status'] === 'approved') {
                $realization->update([
                    'submitted_by' => (string) $kaprodi->id,
                    'submitted_at' => now()->subDays(5),
                    'approved_by' => (string) $ketuaLpm->id,
                    'approved_at' => now()->subDays(2),
                ]);
            } elseif ($scenario['status'] === 'rejected') {
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
