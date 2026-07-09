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

    public function test_super_admin_can_view_traceability_timeline_for_selected_self_assessment(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$selfAssessment, $standard, $indicator] = $this->createSelfAssessmentWithLink($admin);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->set('selfAssessmentId', (string) $selfAssessment->id)
            ->assertSuccessful()
            ->assertSee('Indikator Kelulusan')
            ->assertSee('Standard Pendidikan')
            ->assertSee('Mendefinisikan')
            ->assertDontSee('source_type')
            ->assertDontSee('target_type');
    }

    public function test_timeline_is_hidden_until_self_assessment_is_selected(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        $this->createSelfAssessmentWithLink($admin);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->assertSuccessful()
            ->assertSee('Pilih penilaian mandiri untuk menampilkan timeline.')
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

    public function test_timeline_shows_full_journey_from_standard_source_to_self_assessment(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('super-admin');

        [$selfAssessment, $rootStandard, $source] = $this->createSelfAssessmentWithFullJourney($admin);

        $this->actingAs($admin);

        Livewire::test(ListDataTrails::class)
            ->set('selfAssessmentId', (string) $selfAssessment->id)
            ->assertSuccessful()
            ->assertSee('Dipetakan ke')
            ->assertSee('Berkaitan dengan')
            ->assertSee('Mendefinisikan')
            ->assertSee('Dievaluasi pada');
    }

    /**
     * @return array{0: SelfAssessment, 1: Standard, 2: Indicator}
     */
    private function createSelfAssessmentWithLink(User $admin): array
    {
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

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'UNIT-A',
            'name' => 'Unit A',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $selfAssessment = SelfAssessment::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'status' => 'draft',
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

        SelfAssessmentDetail::query()->create([
            'self_assessment_id' => $selfAssessment->id,
            'realization_id' => $realization->id,
            'score' => 80,
            'created_by' => (string) $admin->id,
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

        return [$selfAssessment, $standard, $indicator];
    }

    /**
     * @return array{0: SelfAssessment}
     */
    private function createSelfAssessmentWithEvaluationLink(User $admin): array
    {
        [$selfAssessment, , $indicator] = $this->createSelfAssessmentWithLink($admin);

        $realization = Realization::query()->first();

        TraceabilityLinks::query()->create([
            'source_type' => 'realization',
            'source_id' => $realization->id,
            'target_type' => 'self_assessment',
            'target_id' => $selfAssessment->id,
            'relation_type' => 'evaluated_in',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        return [$selfAssessment];
    }

    /**
     * @return array{0: SelfAssessment, 1: Standard, 2: StandardSource}
     */
    private function createSelfAssessmentWithFullJourney(User $admin): array
    {
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $rootStandard = Standard::create([
            'code' => 'ROOT',
            'name' => 'Root Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $childStandard = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $rootStandard);

        $indicator = Indicator::factory()->create([
            'name' => 'Indikator Kelulusan',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion');
        $indicator->standardVersion?->update(['standard_id' => $childStandard->id]);

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'UNIT-A',
            'name' => 'Unit A',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $selfAssessment = SelfAssessment::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'status' => 'draft',
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

        SelfAssessmentDetail::query()->create([
            'self_assessment_id' => $selfAssessment->id,
            'realization_id' => $realization->id,
            'score' => 80,
            'created_by' => (string) $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $rootStandard->id,
            'target_type' => 'standard_source',
            'target_id' => $source->id,
            'relation_type' => 'mapped_to',
            'performed_at' => now()->subDays(3),
            'created_by' => $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $rootStandard->id,
            'target_type' => 'standard',
            'target_id' => $childStandard->id,
            'relation_type' => 'related_to',
            'performed_at' => now()->subDays(2),
            'created_by' => $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'standard',
            'source_id' => $childStandard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
            'performed_at' => now()->subDay(),
            'created_by' => $admin->id,
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

        return [$selfAssessment, $rootStandard, $source];
    }
}
