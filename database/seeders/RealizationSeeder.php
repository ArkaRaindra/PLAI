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
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $units = OrganizationUnit::whereIn('type', ['PROGRAM STUDI', 'UPM', 'P3M'])->get();
        $kaprodi = User::where('email', 'kaprodi@example.com')->first() ?? User::where('role', 'kaprodi')->first() ?? User::first();
        $ketuaLpm = User::where('email', 'ketualpm@example.com')->first() ?? User::where('role', 'ketua-lpm')->first() ?? User::first();

        if ($units->isEmpty()) {
            return;
        }

        $targets = Target::with('indicator')->get();

        Auth::setUser($admin);

        foreach ($targets as $target) {
            $unit = $units->random();

            $achievementType = fake()->randomElement(['over', 'under', 'near']);
            $targetValue = (float) $target->target_value;

            $actualValue = match ($achievementType) {
                'over' => $targetValue + fake()->randomFloat(2, 5, 25),
                'under' => max(1, $targetValue - fake()->randomFloat(2, 5, 25)),
                'near' => $targetValue + fake()->randomFloat(2, -5, 5),
            };

            $realization = Realization::query()->firstOrCreate(
                [
                    'target_id' => $target->id,
                    'organization_unit_id' => $unit->id,
                ],
                [
                    'actual_value' => round($actualValue, 2),
                    'score' => fake()->randomFloat(2, 50, 100),
                    'notes' => 'Realisasi capaian untuk '.$target->indicator->name.' ('.$unit->name.')',
                    'status' => 'approved',
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );

            $realization->update([
                'submitted_by' => (string) $kaprodi->id,
                'submitted_at' => now()->subDays(5),
                'approved_by' => (string) $ketuaLpm->id,
                'approved_at' => now()->subDays(2),
            ]);
        }
    }
}
