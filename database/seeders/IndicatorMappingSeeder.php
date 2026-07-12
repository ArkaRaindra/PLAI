<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorMapping;
use App\Models\User;
use Illuminate\Database\Seeder;

class IndicatorMappingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $indicators = Indicator::all();

        if ($indicators->count() < 2) {
            return;
        }

        $internalIndicators = $indicators->slice(0, 3);
        $externalIndicators = $indicators->slice(3, 3);

        if ($externalIndicators->isEmpty()) {
            $externalIndicators = $indicators->slice(1, 3);
        }

        foreach ($internalIndicators as $internal) {
            foreach ($externalIndicators as $external) {
                IndicatorMapping::query()->firstOrCreate(
                    [
                        'internal_indicator_id' => $internal->id,
                        'external_indicator_id' => $external->id,
                    ],
                    [
                        'is_primary' => fake()->boolean(30),
                        'notes' => 'Pemetaan '.$internal->name.' ke '.$external->name,
                        'created_by' => (string) $admin->id,
                        'updated_by' => (string) $admin->id,
                    ]
                );
            }
        }
    }
}
