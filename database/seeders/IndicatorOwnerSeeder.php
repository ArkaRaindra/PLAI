<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Database\Seeder;

class IndicatorOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $indicators = Indicator::all();
        $units = OrganizationUnit::whereIn('type', ['PROGRAM STUDI', 'UPM', 'P3M'])->get();
        $userPositions = UserPosition::all();

        if ($indicators->isEmpty() || $units->isEmpty()) {
            return;
        }

        foreach ($indicators as $index => $indicator) {
            $unit = $units[$index % $units->count()];
            $userPosition = $userPositions->first();

            IndicatorOwner::query()->firstOrCreate(
                [
                    'indicator_id' => $indicator->id,
                    'organization_unit_id' => $unit->id,
                    'user_position_id' => $userPosition?->id,
                ],
                [
                    'is_primary' => true,
                    'notes' => 'Penanggungjawab indikator '.$indicator->name.' di '.$unit->name,
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );
        }
    }
}
