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
        $admin = User::where('email', 'superadmin@example.com')->firstOrFail();
        $positions = Position::all();
        $unit = OrganizationUnit::where('code', 'UPM')->firstOrFail();
        $users = User::all();

        foreach ($users->take(4) as $index => $user) {
            $position = $positions->get($index % $positions->count());

            if ($position) {
                UserPosition::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'organization_unit_id' => $unit->id,
                ], [
                    'start_date' => now()->subYear(),
                    'end_date' => null,
                    'is_active' => true,
                    'created_by' => (string) $admin->id,
                    'updated_by' => (string) $admin->id,
                ]);
            }
        }
    }
}
