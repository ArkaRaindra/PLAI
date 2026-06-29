<?php

namespace Tests\Feature;

use App\Models\QualityPeriod;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\QualityPeriodSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualityPeriodSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_quality_period_seeder_creates_all_periods(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            QualityPeriodSeeder::class,
        ]);

        $this->assertSame(3, QualityPeriod::query()->count());

        $this->assertDatabaseHas('quality_periods', [
            'code' => 'QP_2021',
            'name' => 'Periode Kualitas 2021/2022',
        ]);

        $this->assertDatabaseHas('quality_periods', [
            'code' => 'QP_2025',
            'status' => 'active',
        ]);
    }

    public function test_quality_period_seeder_sets_created_by_to_admin(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            QualityPeriodSeeder::class,
        ]);

        $admin = User::where('email', 'superadmin@example.com')->first();
        $qualityPeriod = QualityPeriod::where('code', 'QP_2025')->first();

        $this->assertEquals($admin->id, $qualityPeriod->created_by);
        $this->assertEquals($admin->id, $qualityPeriod->updated_by);
    }

    public function test_quality_period_seeder_does_not_duplicate_existing_periods(): void
    {
        $admin = User::factory()->create();

        QualityPeriod::create([
            'code' => 'QP_2025',
            'name' => 'Existing Period',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            QualityPeriodSeeder::class,
        ]);

        $this->assertSame(3, QualityPeriod::query()->count());
        $this->assertSame('Existing Period', QualityPeriod::where('code', 'QP_2025')->first()->name);
    }
}
