<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_position_seeder_creates_all_positions(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
        ]);

        $this->assertSame(8, Position::query()->count());

        $this->assertDatabaseHas('positions', [
            'code' => 'KTU_LPM',
            'name' => 'Ketua LPM',
        ]);

        $this->assertDatabaseHas('positions', [
            'code' => 'AUD',
            'name' => 'Auditor',
        ]);

        $this->assertDatabaseHas('positions', [
            'code' => 'DSN',
            'name' => 'Dosen',
        ]);
    }

    public function test_position_seeder_sets_created_by_to_admin(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
        ]);

        $admin = User::where('email', 'superadmin@example.com')->first();
        $position = Position::where('code', 'KTU_LPM')->first();

        $this->assertEquals($admin->id, $position->created_by);
        $this->assertEquals($admin->id, $position->updated_by);
    }

    public function test_position_seeder_does_not_duplicate_existing_positions(): void
    {
        $admin = User::factory()->create();

        Position::create([
            'code' => 'KTU_LPM',
            'name' => 'Existing Position',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
        ]);

        $this->assertSame(8, Position::query()->count());
        $this->assertSame('Existing Position', Position::where('code', 'KTU_LPM')->first()->name);
    }
}
