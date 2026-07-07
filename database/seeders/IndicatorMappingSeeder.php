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
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $indicators = Indicator::all();
        $internal = $indicators->get(0);
        $external = $indicators->get(1);

        if ($internal && $external) {
            IndicatorMapping::query()->firstOrCreate([
                'internal_indicator_id' => $internal->id,
                'external_indicator_id' => $external->id,
            ], [
                'is_primary' => true,
                'notes' => 'Pemetaan indikator internal ke eksternal',
                'created_by' => (string) $admin->id,
                'updated_by' => (string) $admin->id,
            ]);
        }
    }
}
