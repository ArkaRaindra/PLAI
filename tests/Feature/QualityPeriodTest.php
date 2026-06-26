<?php

namespace Tests\Feature;

use App\Models\QualityPeriod;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QualityPeriodTest extends TestCase
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

    public function test_quality_period_can_be_created(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $qualityPeriod = QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Test Quality Period',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'draft',
            'is_active' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertModelExists($qualityPeriod);
        $this->assertSame('QP_TEST', $qualityPeriod->code);
        $this->assertSame('Test Quality Period', $qualityPeriod->name);
    }

    public function test_quality_period_can_be_updated(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $qualityPeriod = QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Test Quality Period',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $qualityPeriod->update([
            'name' => 'Updated Quality Period',
            'status' => 'active',
            'is_active' => true,
            'updated_by' => $admin->id,
        ]);

        $this->assertSame('Updated Quality Period', $qualityPeriod->fresh()->name);
        $this->assertTrue($qualityPeriod->fresh()->is_active);
    }

    public function test_quality_period_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $qualityPeriod = QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Test Quality Period',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $qualityPeriod->delete();

        $this->assertModelMissing($qualityPeriod);
    }

    public function test_quality_period_code_must_be_unique(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'First Quality Period',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->expectException(QueryException::class);

        QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Second Quality Period',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
    }

    public function test_quality_period_belongs_to_created_by_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $qualityPeriod = QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Test Quality Period',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $qualityPeriod->createdBy->id);
    }

    public function test_quality_period_belongs_to_updated_by_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $qualityPeriod = QualityPeriod::create([
            'code' => 'QP_TEST',
            'name' => 'Test Quality Period',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $qualityPeriod->updatedBy->id);

        $updater = User::factory()->create();
        $updater->assignRole('auditor');

        $qualityPeriod->update([
            'name' => 'Updated',
            'updated_by' => $updater->id,
        ]);

        $this->assertEquals($updater->id, $qualityPeriod->fresh()->updatedBy->id);
    }
}
