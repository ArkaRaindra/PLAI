<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\QualityPeriod;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Seeder;

class TargetSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $indicators = Indicator::all();
        $periods = QualityPeriod::whereIn('status', ['active', 'draft'])->get();

        if ($indicators->isEmpty() || $periods->isEmpty()) {
            return;
        }

        foreach ($indicators as $indicator) {
            foreach ($periods as $period) {
                Target::query()->firstOrCreate(
                    [
                        'indicator_id' => $indicator->id,
                        'quality_period_id' => $period->id,
                    ],
                    [
                        'target_value' => fake()->randomFloat(2, 50, 100),
                        'created_by' => (string) $admin->id,
                        'updated_by' => (string) $admin->id,
                    ]
                );
            }
        }
    }
}
