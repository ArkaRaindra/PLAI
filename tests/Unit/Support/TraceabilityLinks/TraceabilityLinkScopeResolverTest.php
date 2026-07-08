<?php

namespace Tests\Unit\Support\TraceabilityLinks;

use App\Events\TraceabilityRecorded;
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
use App\Support\TraceabilityLinks\TraceabilityLinkScopeResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraceabilityLinkScopeResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_returns_no_results_without_standard_source(): void
    {
        $admin = User::factory()->create();
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        TraceabilityLinks::query()->create([
            'source_type' => 'indicator',
            'source_id' => $indicator->id,
            'target_type' => 'indicator',
            'target_id' => $indicator->id,
            'relation_type' => 'related_to',
            'performed_at' => now(),
            'created_by' => $admin->id,
        ]);

        $results = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [])->get();

        $this->assertCount(0, $results);
    }

    public function test_apply_includes_links_for_entities_within_standard_source_scope(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $otherSource = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $standard = $indicator->standardVersion?->standard;
        $standard?->update(['standard_source_id' => $source->id]);

        $otherIndicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $otherIndicator->loadMissing('standardVersion.standard');
        $otherStandard = $otherIndicator->standardVersion?->standard;
        $otherStandard?->update(['standard_source_id' => $otherSource->id]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $standard,
            target: $indicator,
            relationType: 'defines',
        );

        TraceabilityRecorded::dispatch(
            source: $otherStandard,
            target: $otherIndicator,
            relationType: 'defines',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame($indicator->id, $scoped->first()->target_id);
    }

    public function test_apply_narrows_results_by_standard_id(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicatorA = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorA->loadMissing('standardVersion.standard');
        $standardA = $indicatorA->standardVersion?->standard;
        $standardA?->update(['standard_source_id' => $source->id]);

        $indicatorB = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorB->loadMissing('standardVersion.standard');
        $standardB = $indicatorB->standardVersion?->standard;
        $standardB?->update(['standard_source_id' => $source->id]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(source: $standardA, target: $indicatorA, relationType: 'defines');
        TraceabilityRecorded::dispatch(source: $standardB, target: $indicatorB, relationType: 'defines');

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
            'standard_id' => $standardA->id,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame($indicatorA->id, $scoped->first()->target_id);
    }

    public function test_apply_includes_standard_to_standard_source_mapping(): void
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

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $standard,
            target: $source,
            relationType: 'mapped_to',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame('mapped_to', $scoped->first()->relation_type);
    }

    public function test_apply_includes_realization_to_self_assessment_link_in_scope(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $indicator->standardVersion?->standard?->update(['standard_source_id' => $source->id]);
        $qualityPeriodId = $indicator->standardVersion->quality_period_id;

        $organizationUnit = OrganizationUnit::query()->create([
            'code' => 'UNIT-A',
            'name' => 'Unit A',
            'type' => 'UNIT',
            'is_active' => true,
            'created_by' => (string) $admin->id,
        ]);

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $qualityPeriodId,
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
            'quality_period_id' => $qualityPeriodId,
            'status' => 'draft',
            'created_by' => (string) $admin->id,
        ]);

        SelfAssessmentDetail::query()->create([
            'self_assessment_id' => $selfAssessment->id,
            'realization_id' => $realization->id,
            'score' => 80,
            'created_by' => (string) $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $selfAssessment,
            relationType: 'evaluated_in',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
        ])->get();

        $this->assertTrue($scoped->contains(
            fn (TraceabilityLinks $link): bool => $link->relation_type === 'evaluated_in'
                && $link->target_type === 'self_assessment'
                && $link->target_id === $selfAssessment->id,
        ));
    }

    public function test_apply_excludes_links_when_only_one_endpoint_is_in_scope(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $otherSource = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicator->loadMissing('standardVersion.standard');
        $indicator->standardVersion?->standard?->update(['standard_source_id' => $source->id]);

        $otherIndicator = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $indicator,
            target: $otherIndicator,
            relationType: 'mapped_to',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
        ])->get();

        $this->assertCount(0, $scoped);
    }

    public function test_apply_narrows_results_by_quality_period(): void
    {
        $admin = User::factory()->create();
        $source = StandardSource::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $indicatorA = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorA->loadMissing('standardVersion.standard');
        $standardA = $indicatorA->standardVersion?->standard;
        $standardA?->update(['standard_source_id' => $source->id]);
        $periodA = $indicatorA->standardVersion->quality_period_id;

        $indicatorB = Indicator::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $indicatorB->loadMissing('standardVersion.standard');
        $standardB = $indicatorB->standardVersion?->standard;
        $standardB?->update(['standard_source_id' => $source->id]);

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(source: $standardA, target: $indicatorA, relationType: 'defines');
        TraceabilityRecorded::dispatch(source: $standardB, target: $indicatorB, relationType: 'defines');

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'standard_source_id' => $source->id,
            'quality_period_id' => $periodA,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame($indicatorA->id, $scoped->first()->target_id);
    }
}
