<?php

namespace App\Support\TraceabilityLinks;

use App\Models\Indicator;
use App\Models\IndicatorOwner;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentDetail;
use App\Models\Standard;
use App\Models\StandardVersion;
use App\Models\Target;
use Illuminate\Database\Eloquent\Builder;

class TraceabilityLinkScopeResolver
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public static function apply(Builder $query, array $filters): Builder
    {
        $standardSourceId = filled($filters['standard_source_id'] ?? null)
            ? (int) $filters['standard_source_id']
            : null;

        if ($standardSourceId === null) {
            return $query->whereRaw('1 = 0');
        }

        $qualityPeriodId = filled($filters['quality_period_id'] ?? null)
            ? (int) $filters['quality_period_id']
            : null;
        $standardVersionId = filled($filters['standard_version_id'] ?? null)
            ? (int) $filters['standard_version_id']
            : null;
        $standardId = filled($filters['standard_id'] ?? null)
            ? (int) $filters['standard_id']
            : null;

        $standardIdsQuery = self::standardIdsQuery(
            $standardSourceId,
            $qualityPeriodId,
            $standardVersionId,
            $standardId,
        );

        $standardVersionIdsQuery = self::standardVersionIdsQuery(
            $standardIdsQuery,
            $qualityPeriodId,
            $standardVersionId,
        );

        $indicatorIdsQuery = Indicator::query()
            ->whereIn('standard_version_id', $standardVersionIdsQuery)
            ->select('id');

        $targetIdsQuery = Target::query()
            ->whereIn('indicator_id', $indicatorIdsQuery)
            ->when(
                $qualityPeriodId,
                fn (Builder $targetQuery): Builder => $targetQuery->where('quality_period_id', $qualityPeriodId),
            )
            ->select('id');

        $realizationIdsQuery = Realization::query()
            ->whereIn('target_id', $targetIdsQuery)
            ->select('id');

        $organizationUnitIdsQuery = IndicatorOwner::query()
            ->whereIn('indicator_id', $indicatorIdsQuery)
            ->select('organization_unit_id');

        $selfAssessmentIdsQuery = self::selfAssessmentIdsQuery(
            $qualityPeriodId,
            $realizationIdsQuery,
        );

        return $query
            ->where(fn (Builder $linkQuery): Builder => self::whereEndpointInScope(
                $linkQuery,
                'source',
                $standardSourceId,
                $standardIdsQuery,
                $indicatorIdsQuery,
                $targetIdsQuery,
                $realizationIdsQuery,
                $selfAssessmentIdsQuery,
                $organizationUnitIdsQuery,
            ))
            ->where(fn (Builder $linkQuery): Builder => self::whereEndpointInScope(
                $linkQuery,
                'target',
                $standardSourceId,
                $standardIdsQuery,
                $indicatorIdsQuery,
                $targetIdsQuery,
                $realizationIdsQuery,
                $selfAssessmentIdsQuery,
                $organizationUnitIdsQuery,
            ));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function hasScope(array $filters): bool
    {
        return filled($filters['standard_source_id'] ?? null);
    }

    private static function standardIdsQuery(
        int $standardSourceId,
        ?int $qualityPeriodId,
        ?int $standardVersionId,
        ?int $standardId,
    ): Builder {
        return Standard::query()
            ->where('standard_source_id', $standardSourceId)
            ->when(
                $standardId,
                fn (Builder $query): Builder => $query->whereKey($standardId),
            )
            ->when(
                $standardVersionId,
                fn (Builder $query): Builder => $query->whereHas(
                    'standardVersions',
                    fn (Builder $versionQuery): Builder => $versionQuery->whereKey($standardVersionId),
                ),
            )
            ->when(
                $qualityPeriodId && ! $standardVersionId,
                fn (Builder $query): Builder => $query->whereHas(
                    'standardVersions',
                    fn (Builder $versionQuery): Builder => $versionQuery->where('quality_period_id', $qualityPeriodId),
                ),
            )
            ->select('id');
    }

    private static function standardVersionIdsQuery(
        Builder $standardIdsQuery,
        ?int $qualityPeriodId,
        ?int $standardVersionId,
    ): Builder {
        return StandardVersion::query()
            ->whereIn('standard_id', $standardIdsQuery)
            ->when(
                $qualityPeriodId,
                fn (Builder $query): Builder => $query->where('quality_period_id', $qualityPeriodId),
            )
            ->when(
                $standardVersionId,
                fn (Builder $query): Builder => $query->whereKey($standardVersionId),
            )
            ->select('id');
    }

    private static function selfAssessmentIdsQuery(
        ?int $qualityPeriodId,
        Builder $realizationIdsQuery,
    ): Builder {
        return SelfAssessment::query()
            ->whereIn(
                'id',
                SelfAssessmentDetail::query()
                    ->whereIn('realization_id', $realizationIdsQuery)
                    ->select('self_assessment_id'),
            )
            ->when(
                $qualityPeriodId,
                fn (Builder $query): Builder => $query->where('quality_period_id', $qualityPeriodId),
            )
            ->select('id');
    }

    private static function whereEndpointInScope(
        Builder $query,
        string $side,
        int $standardSourceId,
        Builder $standardIdsQuery,
        Builder $indicatorIdsQuery,
        Builder $targetIdsQuery,
        Builder $realizationIdsQuery,
        Builder $selfAssessmentIdsQuery,
        Builder $organizationUnitIdsQuery,
    ): Builder {
        $typeColumn = "{$side}_type";
        $idColumn = "{$side}_id";

        return $query->where(function (Builder $scopeQuery) use (
            $typeColumn,
            $idColumn,
            $standardSourceId,
            $standardIdsQuery,
            $indicatorIdsQuery,
            $targetIdsQuery,
            $realizationIdsQuery,
            $selfAssessmentIdsQuery,
            $organizationUnitIdsQuery,
        ): void {
            $scopeQuery
                ->where(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'standard_source')
                        ->where($idColumn, $standardSourceId),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'standard')
                        ->whereIn($idColumn, clone $standardIdsQuery),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'indicator')
                        ->whereIn($idColumn, clone $indicatorIdsQuery),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'target')
                        ->whereIn($idColumn, clone $targetIdsQuery),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'realization')
                        ->whereIn($idColumn, clone $realizationIdsQuery),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'self_assessment')
                        ->whereIn($idColumn, clone $selfAssessmentIdsQuery),
                )
                ->orWhere(
                    fn (Builder $endpointQuery): Builder => $endpointQuery
                        ->where($typeColumn, 'organization_unit')
                        ->whereIn($idColumn, clone $organizationUnitIdsQuery),
                );
        });
    }
}
