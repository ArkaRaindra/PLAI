<?php

namespace Tests\Feature;

use App\Events\TraceabilityRecorded;
use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\Target;
use App\Models\TraceabilityLinks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraceabilityRecordedTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_creates_traceability_link_between_parent_and_child_standard(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $parent = Standard::create([
            'code' => 'PARENT',
            'name' => 'Parent Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $child = Standard::create([
            'code' => 'CHILD',
            'name' => 'Child Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ], $parent);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $parent,
            target: $child,
            relationType: 'related_to',
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'standard',
            'source_id' => $parent->id,
            'target_type' => 'standard',
            'target_id' => $child->id,
            'relation_type' => 'related_to',
        ]);
    }

    public function test_dispatch_persists_metadata_on_traceability_link(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $standard = Standard::create([
            'code' => 'ROOT',
            'name' => 'Root Standard',
            'standard_source_id' => $source->id,
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $metadata = [
            'code' => $standard->code,
            'name' => $standard->name,
            'standard_source_id' => $standard->standard_source_id,
        ];

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $standard,
            target: $standard->standardSource,
            relationType: 'mapped_to',
            metadata: $metadata,
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'standard_source',
            'target_id' => $source->id,
            'relation_type' => 'mapped_to',
        ]);

        $this->assertSame($metadata, TraceabilityLinks::query()->sole()->metadata);
    }

    public function test_dispatch_creates_traceability_link_between_standard_and_indicator(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $standard = $indicator->standardVersion?->standard;

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $standard,
            target: $indicator,
            relationType: 'defines',
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'standard',
            'source_id' => $standard->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'defines',
        ]);
    }

    public function test_dispatch_creates_traceability_link_between_indicator_and_target(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'target_value' => 100,
            'created_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $indicator,
            target: $target,
            relationType: 'defines',
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'target',
            'target_id' => $target->id,
            'relation_type' => 'defines',
        ]);
    }

    public function test_dispatch_creates_traceability_link_between_realization_and_self_assessment(): void
    {
        $admin = User::factory()->create();
        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $selfAssessment = SelfAssessment::query()->create([
            'organization_unit_id' => $organizationUnit->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);
        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $selfAssessment->quality_period_id,
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

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $selfAssessment,
            relationType: 'evaluated_in',
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'realization',
            'source_id' => $realization->id,
            'target_type' => 'self_assessment',
            'target_id' => $selfAssessment->id,
            'relation_type' => 'evaluated_in',
        ]);
    }

    public function test_dispatch_creates_traceability_link_between_indicators_via_mapping(): void
    {
        $admin = User::factory()->create();
        $internal = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $external = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $internal,
            target: $external,
            relationType: 'mapped_to',
            metadata: ['is_primary' => true],
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'indicator',
            'source_id' => $internal->id,
            'target_type' => 'indicator',
            'target_id' => $external->id,
            'relation_type' => 'mapped_to',
        ]);
    }

    public function test_dispatch_creates_traceability_link_between_realization_and_target(): void
    {
        $admin = User::factory()->create();
        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
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
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $target,
            relationType: 'measured_by',
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'realization',
            'source_id' => $realization->id,
            'target_type' => 'target',
            'target_id' => $target->id,
            'relation_type' => 'measured_by',
        ]);
    }

    public function test_dispatch_creates_traceability_link_between_indicator_and_organization_unit(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $indicator,
            target: $organizationUnit,
            relationType: 'supported_by',
            metadata: ['is_primary' => true],
        );

        $this->assertDatabaseHas('traceability_links', [
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'organization_unit',
            'target_id' => $organizationUnit->id,
            'relation_type' => 'supported_by',
        ]);
    }
}
