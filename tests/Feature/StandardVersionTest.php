<?php

namespace Tests\Feature;

use App\Models\QualityPeriode;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use App\Models\User;
use App\Support\StandardVersionPersister;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandardVersionTest extends TestCase
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

    public function test_create_standard_without_version_does_not_create_standard_version(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'NO-VER',
            'name' => 'Standard Without Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => false,
            'quality_period_mode' => 'existing',
            'quality_period_id' => null,
            'qualityPeriode' => [],
            'standardVersion' => [],
        ]);

        $this->assertDatabaseCount('standards', 1);
        $this->assertDatabaseCount('standard_versions', 0);
    }

    public function test_create_standard_with_existing_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriode::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'WITH-VER',
            'name' => 'Standard With Version',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriode' => [],
            'standardVersion' => [
                'version' => 'v1',
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseHas('standard_versions', [
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => 'v1',
        ]);
        $this->assertDatabaseCount('quality_periodes', 1);
    }

    public function test_create_standard_with_new_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'NEW-PER',
            'name' => 'Standard With New Period',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => true,
            'quality_period_mode' => 'new',
            'quality_period_id' => null,
            'qualityPeriode' => [
                'code' => 'QP-2026',
                'name' => 'Periode 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'draft',
                'is_active' => true,
            ],
            'standardVersion' => [
                'version' => 'v1',
                'start_date' => now()->toDateString(),
                'status' => 'draft',
                'is_active' => true,
            ],
        ]);

        $this->assertDatabaseHas('quality_periodes', [
            'code' => 'QP-2026',
            'name' => 'Periode 2026',
        ]);
        $this->assertDatabaseHas('standard_versions', [
            'standard_id' => $standard->id,
            'version' => 'v1',
        ]);
    }

    public function test_edit_removes_standard_version_when_toggle_off(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriode::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'DEL-VER',
            'name' => 'Standard To Unlink',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => 'v1',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standard->refresh();

        StandardVersionPersister::sync($standard, [
            'include_standard_version' => false,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $period->id,
            'qualityPeriode' => [],
            'standardVersion' => [],
        ]);

        $this->assertDatabaseCount('standard_versions', 0);
    }

    public function test_standards_can_be_filtered_by_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodA = QualityPeriode::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $periodB = QualityPeriode::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standardA = Standard::create([
            'code' => 'STD-A',
            'name' => 'Standard A',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standardB = Standard::create([
            'code' => 'STD-B',
            'name' => 'Standard B',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standardA->id,
            'quality_period_id' => $periodA->id,
            'version' => 'v1',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        StandardVersion::factory()->create([
            'standard_id' => $standardB->id,
            'quality_period_id' => $periodB->id,
            'version' => 'v1',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $filtered = Standard::query()
            ->where('standard_source_id', $source->id)
            ->whereHas('standardVersions', fn ($query) => $query->where('quality_period_id', $periodA->id))
            ->pluck('code')
            ->all();

        $this->assertSame(['STD-A'], $filtered);
    }

    public function test_standards_without_filter_show_all_in_source(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        Standard::create([
            'code' => 'STD-1',
            'name' => 'Standard 1',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Standard::create([
            'code' => 'STD-2',
            'name' => 'Standard 2',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $count = Standard::query()
            ->where('standard_source_id', $source->id)
            ->count();

        $this->assertSame(2, $count);
    }

    public function test_to_form_data_prefills_version_fields(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $period = QualityPeriode::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

        $standard = Standard::create([
            'code' => 'FILL',
            'name' => 'Standard Fill',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $version = StandardVersion::factory()->create([
            'standard_id' => $standard->id,
            'quality_period_id' => $period->id,
            'version' => 'v2',
            'status' => 'active',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $formData = StandardVersionPersister::toFormData($version);

        $this->assertTrue($formData['include_standard_version']);
        $this->assertSame('existing', $formData['quality_period_mode']);
        $this->assertSame($period->id, $formData['quality_period_id']);
        $this->assertSame('v2', $formData['standardVersion']['version']);
    }
}
