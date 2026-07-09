<?php

namespace App\Support\TraceabilityLinks;

use App\Models\IndicatorOwner;
use App\Models\SelfAssessment;
use App\Models\Standard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TraceabilityLinkScopeResolver
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public static function apply(Builder $query, array $filters): Builder
    {
        if (! self::hasCompleteScope($filters)) {
            return $query->whereRaw('1 = 0');
        }

        $scope = self::resolveFromSelfAssessment((int) $filters['self_assessment_id']);

        if ($scope === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $scopedQuery) use ($scope): void {
            $scopedQuery
                ->where(function (Builder $linkQuery) use ($scope): void {
                    $linkQuery
                        ->where(fn (Builder $endpointQuery): Builder => self::whereEndpointInScope($endpointQuery, 'source', $scope))
                        ->where(fn (Builder $endpointQuery): Builder => self::whereEndpointInScope($endpointQuery, 'target', $scope));
                })
                ->orWhere(function (Builder $linkQuery) use ($scope): void {
                    if ($scope['self_assessment_ids'] === []) {
                        return;
                    }

                    $linkQuery
                        ->where('relation_type', 'evaluated_in')
                        ->where('target_type', 'self_assessment')
                        ->whereIn('target_id', $scope['self_assessment_ids']);
                });
        });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function hasCompleteScope(array $filters): bool
    {
        return filled($filters['self_assessment_id'] ?? null);
    }

    /**
     * @return array{
     *     standard_source_ids: array<int, int>,
     *     standard_ids: array<int, int>,
     *     indicator_ids: array<int, int>,
     *     target_ids: array<int, int>,
     *     realization_ids: array<int, int>,
     *     self_assessment_ids: array<int, int>,
     *     organization_unit_ids: array<int, int>,
     * }|null
     */
    public static function resolveFromSelfAssessment(int $selfAssessmentId): ?array
    {
        $selfAssessment = SelfAssessment::query()
            ->with([
                'details.realization.target.indicator.standardVersion.standard',
            ])
            ->find($selfAssessmentId);

        if ($selfAssessment === null) {
            return null;
        }

        $realizations = $selfAssessment->details
            ->map(fn ($detail) => $detail->realization)
            ->filter();

        $targets = $realizations
            ->map(fn ($realization) => $realization->target)
            ->filter();

        $indicators = $targets
            ->map(fn ($target) => $target->indicator)
            ->filter();

        $standards = self::standardsWithAncestors(
            $indicators
                ->map(fn ($indicator) => $indicator->standardVersion?->standard)
                ->filter(),
        );

        $indicatorIds = $indicators->pluck('id')->unique()->values()->all();

        $organizationUnitIds = collect([$selfAssessment->organization_unit_id])
            ->merge(
                IndicatorOwner::query()
                    ->whereIn('indicator_id', $indicatorIds)
                    ->pluck('organization_unit_id'),
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'standard_source_ids' => $standards
                ->pluck('standard_source_id')
                ->filter()
                ->unique()
                ->values()
                ->all(),
            'standard_ids' => $standards->pluck('id')->unique()->values()->all(),
            'indicator_ids' => $indicatorIds,
            'target_ids' => $targets->pluck('id')->unique()->values()->all(),
            'realization_ids' => $realizations->pluck('id')->unique()->values()->all(),
            'self_assessment_ids' => [$selfAssessment->id],
            'organization_unit_ids' => $organizationUnitIds,
        ];
    }

    /**
     * @param  Collection<int, Standard>  $standards
     * @return Collection<int, Standard>
     */
    private static function standardsWithAncestors(Collection $standards): Collection
    {
        return $standards
            ->flatMap(
                fn (Standard $standard) => $standard->ancestors()->get()->push($standard),
            )
            ->unique('id')
            ->values();
    }

    /**
     * @param  array{
     *     standard_source_ids: array<int, int>,
     *     standard_ids: array<int, int>,
     *     indicator_ids: array<int, int>,
     *     target_ids: array<int, int>,
     *     realization_ids: array<int, int>,
     *     self_assessment_ids: array<int, int>,
     *     organization_unit_ids: array<int, int>,
     * }  $scope
     */
    private static function whereEndpointInScope(Builder $query, string $side, array $scope): Builder
    {
        $typeColumn = "{$side}_type";
        $idColumn = "{$side}_id";

        return $query->where(function (Builder $scopeQuery) use ($typeColumn, $idColumn, $scope): void {
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'standard_source', $scope['standard_source_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'standard', $scope['standard_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'indicator', $scope['indicator_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'target', $scope['target_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'realization', $scope['realization_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'self_assessment', $scope['self_assessment_ids']);
            self::orWhereMorphInIds($scopeQuery, $typeColumn, $idColumn, 'organization_unit', $scope['organization_unit_ids']);
        });
    }

    /**
     * @param  array<int, int>  $ids
     */
    private static function orWhereMorphInIds(
        Builder $query,
        string $typeColumn,
        string $idColumn,
        string $type,
        array $ids,
    ): void {
        if ($ids === []) {
            return;
        }

        $query->orWhere(
            fn (Builder $endpointQuery): Builder => $endpointQuery
                ->where($typeColumn, $type)
                ->whereIn($idColumn, $ids),
        );
    }
}
