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

    public function test_apply_returns_no_results_without_self_assessment(): void
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

        $this->assertCount(0, TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [])->get());
    }

    public function test_apply_includes_links_for_entities_within_self_assessment_scope(): void
    {
        [$selfAssessment, $indicator, $standard] = $this->createSelfAssessmentContext(withRealization: true);

        $this->actingAs(User::factory()->create());

        TraceabilityRecorded::dispatch(
            source: $standard,
            target: $indicator,
            relationType: 'defines',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'self_assessment_id' => $selfAssessment->id,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame($indicator->id, $scoped->first()->target_id);
    }

    public function test_apply_includes_realization_to_self_assessment_link_in_scope(): void
    {
        [$selfAssessment, $indicator, $standard, $realization] = $this->createSelfAssessmentContext(withRealization: true);

        $this->actingAs(User::factory()->create());

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $selfAssessment,
            relationType: 'evaluated_in',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'self_assessment_id' => $selfAssessment->id,
        ])->get();

        $this->assertCount(1, $scoped);
        $this->assertSame('evaluated_in', $scoped->first()->relation_type);
    }

    public function test_apply_includes_evaluated_in_link_for_scoped_self_assessment_even_without_realization_in_scope(): void
    {
        [$selfAssessment, $indicator, $standard] = $this->createSelfAssessmentContext(withRealization: false);
        $admin = User::factory()->create();

        $target = Target::query()->create([
            'indicator_id' => $indicator->id,
            'quality_period_id' => $indicator->standardVersion->quality_period_id,
            'target_value' => 100,
            'created_by' => (string) $admin->id,
        ]);

        $realization = Realization::query()->create([
            'target_id' => $target->id,
            'organization_unit_id' => $selfAssessment->organization_unit_id,
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

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'self_assessment_id' => $selfAssessment->id,
        ])->get();

        $this->assertTrue($scoped->contains(
            fn (TraceabilityLinks $link): bool => $link->relation_type === 'evaluated_in'
                && $link->target_id === $selfAssessment->id,
        ));
    }

    public function test_apply_excludes_links_when_only_one_endpoint_is_in_scope(): void
    {
        [$selfAssessment, $indicator] = $this->createSelfAssessmentContext(withRealization: true);
        $otherIndicator = Indicator::factory()->create([
            'created_by' => User::factory()->create()->id,
            'updated_by' => User::factory()->create()->id,
        ]);

        $this->actingAs(User::factory()->create());

        TraceabilityRecorded::dispatch(
            source: $indicator,
            target: $otherIndicator,
            relationType: 'mapped_to',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'self_assessment_id' => $selfAssessment->id,
        ])->get();

        $this->assertCount(0, $scoped);
    }

    public function test_resolve_from_self_assessment_collects_related_entity_ids(): void
    {
        [$selfAssessment, $indicator, $standard, $realization, $target] = $this->createSelfAssessmentContext(withRealization: true);

        $scope = TraceabilityLinkScopeResolver::resolveFromSelfAssessment($selfAssessment->id);

        $this->assertNotNull($scope);
        $this->assertContains($selfAssessment->id, $scope['self_assessment_ids']);
        $this->assertContains($realization->id, $scope['realization_ids']);
        $this->assertContains($target->id, $scope['target_ids']);
        $this->assertContains($indicator->id, $scope['indicator_ids']);
        $this->assertContains($standard->id, $scope['standard_ids']);
        $this->assertContains($selfAssessment->organization_unit_id, $scope['organization_unit_ids']);
    }

    public function test_resolve_from_self_assessment_includes_ancestor_standards(): void
    {
        $admin = User::factory()->create();
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

        $scope = TraceabilityLinkScopeResolver::resolveFromSelfAssessment($selfAssessment->id);

        $this->assertNotNull($scope);
        $this->assertContains($rootStandard->id, $scope['standard_ids']);
        $this->assertContains($childStandard->id, $scope['standard_ids']);
    }

    public function test_apply_includes_standard_source_and_parent_standard_links(): void
    {
        $admin = User::factory()->create();
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

        $this->actingAs($admin);

        TraceabilityRecorded::dispatch(
            source: $rootStandard,
            target: $source,
            relationType: 'mapped_to',
        );

        TraceabilityRecorded::dispatch(
            source: $rootStandard,
            target: $childStandard,
            relationType: 'related_to',
        );

        TraceabilityRecorded::dispatch(
            source: $childStandard,
            target: $indicator,
            relationType: 'defines',
        );

        TraceabilityRecorded::dispatch(
            source: $realization,
            target: $selfAssessment,
            relationType: 'evaluated_in',
        );

        $scoped = TraceabilityLinkScopeResolver::apply(TraceabilityLinks::query(), [
            'self_assessment_id' => $selfAssessment->id,
        ])->get();

        $this->assertCount(4, $scoped);
        $this->assertTrue($scoped->contains(
            fn (TraceabilityLinks $link): bool => $link->relation_type === 'mapped_to'
                && $link->source_type === 'standard'
                && $link->target_type === 'standard_source',
        ));
        $this->assertTrue($scoped->contains(
            fn (TraceabilityLinks $link): bool => $link->relation_type === 'evaluated_in'
                && $link->target_type === 'self_assessment',
        ));
    }

    /**
     * @return array{0: SelfAssessment, 1: Indicator, 2: Standard, 3?: Realization, 4?: Target}
     */
    private function createSelfAssessmentContext(bool $withRealization = false): array
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
        $standard = $indicator->standardVersion?->standard;
        $standard?->update(['standard_source_id' => $source->id]);

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

        if (! $withRealization) {
            return [$selfAssessment, $indicator, $standard];
        }

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

        return [$selfAssessment, $indicator, $standard, $realization, $target];
    }
}
