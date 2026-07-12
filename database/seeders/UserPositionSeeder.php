<?php

namespace Database\Seeders;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Database\Seeder;

class UserPositionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'superadmin@example.com')->first() ?? User::first();
        $positions = Position::all();
        $units = OrganizationUnit::whereIn('type', ['PROGRAM STUDI', 'UPM', 'P3M'])->get();
        $users = User::all();

        if ($positions->isEmpty() || $units->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($users as $userIndex => $user) {
            $position = $positions[$userIndex % $positions->count()];
            $unit = $units[$userIndex % $units->count()];

            UserPosition::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'organization_unit_id' => $unit->id,
                ],
                [
                    'start_date' => now()->subYear(),
                    'end_date' => null,
                    'is_active' => true,
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]
            );
        }
    }
}
