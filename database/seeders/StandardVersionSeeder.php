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
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $std001 = Standard::where('code', 'STD-001')->firstOrFail();
        $std004 = Standard::where('code', 'STD-004')->firstOrFail();

        $periods = QualityPeriod::where('code', 'QP_2025')->get();

        foreach ($periods as $period) {
            StandardVersion::query()->firstOrCreate([
                'standard_id' => $std001->id,
                'quality_period_id' => $period->id,
                'version' => 'v1',
            ], [
                'start_date' => $period->start_date,
                'end_date' => $period->end_date,
                'status' => $period->status === 'active' ? 'active' : 'closed',
                'is_active' => true,
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);

            StandardVersion::query()->firstOrCreate([
                'standard_id' => $std004->id,
                'quality_period_id' => $period->id,
                'version' => 'v1',
            ], [
                'start_date' => $period->start_date,
                'end_date' => $period->end_date,
                'status' => $period->status === 'active' ? 'active' : 'closed',
                'is_active' => true,
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);
        }
    }
}
