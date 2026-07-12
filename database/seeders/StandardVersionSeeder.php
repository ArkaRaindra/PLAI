<?php

namespace Database\Seeders;

use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardVersion;
use App\Models\User;
use Illuminate\Database\Seeder;

class StandardVersionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $standards = Standard::all();
        $periods = QualityPeriod::whereIn('status', ['active', 'closed'])->get();

        if ($standards->isEmpty() || $periods->isEmpty()) {
            return;
        }

        foreach ($standards as $standard) {
            foreach ($periods as $period) {
                $versionNumber = 1;

                StandardVersion::query()->firstOrCreate(
                    [
                        'standard_id' => $standard->id,
                        'quality_period_id' => $period->id,
                        'version' => 'v'.$versionNumber,
                    ],
                    [
                        'start_date' => $period->start_date,
                        'end_date' => $period->end_date,
                        'status' => $period->status,
                        'is_active' => true,
                        'created_by' => (string) $admin->id,
                        'updated_by' => (string) $admin->id,
                    ]
                );
            }
        }
    }
}
