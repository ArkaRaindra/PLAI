<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Pages\TargetAchievementMonitoring;
use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\Target;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TargetAchievementMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_super_admin_can_access_the_monitoring_page(): void
    {
        $admin = $this->makeSuperAdmin();

        $this->actingAs($admin)
            ->get(TargetAchievementMonitoring::getUrl(panel: 'super-admin'))
            ->assertSuccessful();
    }

    public function test_it_lists_realizations_with_computed_achievement_and_status(): void
    {
        $admin = $this->makeSuperAdmin();
        $this->actingAs($admin);

        $period = QualityPeriod::factory()->create([
            'status' => 'active',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $unit = OrganizationUnit::query()->create([
            'code' => 'UNIT-'.fake()->unique()->numerify('###'),
            'name' => 'Unit '.fake()->word(),
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $achieved = $this->makeMonitoringRecord($admin, period: $period, unit: $unit, targetValue: 100, actualValue: 120, status: 'approved');
        $notAchieved = $this->makeMonitoringRecord($admin, period: $period, unit: $unit, targetValue: 100, actualValue: 40, status: 'approved');
        $inProgress = $this->makeMonitoringRecord($admin, period: $period, unit: $unit, targetValue: 100, actualValue: 10, status: 'submitted');

        $response = $this->get(TargetAchievementMonitoring::getUrl(panel: 'super-admin'));

        $response->assertSuccessful();
        $response->assertSee('Tercapai');
        $response->assertSee('Belum Tercapai');
        $response->assertSee('Dalam Proses');
    }

    private function makeSuperAdmin(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('super-admin');

        return $user;
    }

    private function makeMonitoringRecord(User $creator, QualityPeriod $period, OrganizationUnit $unit, float $targetValue, float $actualValue, string $status): IndicatorOwner
    {
        $indicator = Indicator::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $period->id,
            'target_value' => $targetValue,
            'created_by' => (string) $creator->id,
        ]);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $unit->id,
            'actual_value' => $actualValue,
            'score' => 8,
            'status' => $status,
            'created_by' => (string) $creator->id,
        ]);

        return IndicatorOwner::query()->create([
            'indicator_id' => $indicator->id,
            'organization_unit_id' => $unit->id,
            'is_primary' => true,
            'notes' => 'Penanggungjawab indikator ' . $indicator->name,
            'created_by' => (string) $creator->id,
            'updated_by' => (string) $creator->id,
        ]);
    }
}
