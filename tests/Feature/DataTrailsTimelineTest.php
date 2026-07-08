<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\DataTrails\DataTrailsResource;
use App\Filament\SuperAdmin\Resources\DataTrails\Pages\ListDataTrails;
use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentDetail;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\Target;
use App\Models\TraceabilityLinks;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DataTrailsTimelineTest extends TestCase
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

    public function test_super_admin_can_view_traceability_timeline(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $standard = $indicator->standardVersion?->standard;
        $standard?->update([
            'code' => 'STD-01',
            'name' => 'Standard Pendidikan',
            'standard_source_id' => $source->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->set('standardSourceId', (string) $source->id)
            ->assertSuccessful()
            ->assertSee('Indikator Kelulusan')
            ->assertSee('Standard Pendidikan')
            ->assertSee('Mendefinisikan')
            ->assertDontSee('source_type')
            ->assertDontSee('target_type');
    }

    public function test_timeline_is_empty_without_scope_filter(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $standard = Standard::create([
            'code' => 'STD-01',
            'name' => 'Standard Pendidikan',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->assertSuccessful()
            ->assertDontSee('Indikator Kelulusan');
    }

    public function test_data_trails_resource_is_read_only(): void
    {
        $this->assertFalse(DataTrailsResource::canCreate());
    }

    public function test_timeline_orders_links_from_earliest_to_latest(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $standard = Standard::create([
            'code' => 'STD-01',
            'name' => 'Standard Pendidikan',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $earlier = TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
            'performed_at' => now()->subDay(),
            'created_by' => $admin->id,
        ]);

        $later = TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'related_to',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $orderedIds = TraceabilityLinks::query()
            ->orderByRaw('COALESCE(performed_at, created_at)')
            ->pluck('id')
            ->all();

        $this->assertSame([$earlier->id, $later->id], $orderedIds);
    }

    public function test_timeline_shows_self_assessment_link_within_scope(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $indicator->standardVersion?->standard?->update(['standard_source_id' => $source->id]);

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'UNIT-A',
            'name' => 'Unit A',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'target_value' => 100,
            'created_by' => (string) $admin->id,
        ]);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $organizationUnit->id,
            'actual_value' => 10,
            'score' => 80,
            'status' => 'approved',
            'created_by' => (string) $admin->id,
        ]);

        $selfAssessment = SelfAssessment::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        SelfAssessmentDetail::query()->create([
            'self_assessment_id' => $selfAssessment->id,
            'realization_id' => $realization->id,
            'score' => 80,
            'created_by' => (string) $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'realization',
            'source_id' => $realization->id,
            'target_type' => 'self_assessment',
            'target_id' => $selfAssessment->id,
            'relation_type' => 'evaluated_in',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->set('standardSourceId', (string) $source->id)
            ->assertSuccessful()
            ->assertSee('Dievaluasi pada')
            ->assertSee('Penilaian Mandiri Unit A');
    }

    public function test_timeline_narrows_when_quality_period_filter_changes(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicatorA = Indicator::factory()->create([
            'name' => 'Indikator Periode A',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorA->loadMissing('standardVersion.standard');
        $standardA = $indicatorA->standardVersion?->standard;
        $standardA?->update(['standard_source_id' => $source->id]);
        $periodA = $indicatorA->standardVersion->quality_period_id;

        $indicatorB = Indicator::factory()->create([
            'name' => 'Indikator Periode B',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorB->loadMissing('standardVersion.standard');
        $standardB = $indicatorB->standardVersion?->standard;
        $standardB?->update(['standard_source_id' => $source->id]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standardA->id,
            'target_type' => 'indicator',
            'target_id' => $indicatorA->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $standardB->id,
            'target_type' => 'indicator',
            'target_id' => $indicatorB->id,
            'relation_type' => 'defines',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->set('standardSourceId', (string) $source->id)
            ->assertSuccessful()
            ->assertSee('Indikator Periode A')
            ->assertSee('Indikator Periode B')
            ->set('qualityPeriodId', (string) $periodA)
            ->assertSuccessful()
            ->assertSee('Indikator Periode A')
            ->assertDontSee('Indikator Periode B');
    }
}
