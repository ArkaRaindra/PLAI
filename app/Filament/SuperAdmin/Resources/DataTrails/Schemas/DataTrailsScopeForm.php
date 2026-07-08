<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Schemas;

use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\StandardVersion;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;

class DataTrailsScopeForm
{
    /**
     * @return array<int, Select>
     */
    public static function schema(): array
    {
        return [
            Select::make('standardSourceId')
                ->label('Sumber Standar')
                ->placeholder('— Pilih Sumber Standar —')
                ->options(fn (): array => StandardSource::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->required()
                ->live(debounce: 300)
                ->afterStateUpdated(function (Set $set): void {
                    $set('qualityPeriodId', null);
                    $set('standardVersionId', null);
                    $set('standardId', null);
                }),
            Select::make('qualityPeriodId')
                ->label('Periode Kualitas')
                ->placeholder('— Semua Periode —')
                ->options(function (Get $get): array {
                    if (blank($get('standardSourceId'))) {
                        return [];
                    }

                    return QualityPeriod::query()
                        ->whereIn(
                            'id',
                            StandardVersion::query()
                                ->whereHas(
                                    'standard',
                                    fn (Builder $query): Builder => $query->where(
                                        'standard_source_id',
                                        (int) $get('standardSourceId'),
                                    ),
                                )
                                ->select('quality_period_id'),
                        )
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all();
                })
                ->searchable()
                ->live(debounce: 300)
                ->visible(fn (Get $get): bool => filled($get('standardSourceId')))
                ->afterStateUpdated(function (Set $set): void {
                    $set('standardVersionId', null);
                    $set('standardId', null);
                }),
            Select::make('standardVersionId')
                ->label('Versi Standar')
                ->placeholder('— Semua Versi —')
                ->options(function (Get $get): array {
                    if (blank($get('standardSourceId'))) {
                        return [];
                    }

                    return StandardVersion::query()
                        ->with('standard')
                        ->whereHas(
                            'standard',
                            fn (Builder $query): Builder => $query->where(
                                'standard_source_id',
                                (int) $get('standardSourceId'),
                            ),
                        )
                        ->when(
                            filled($get('qualityPeriodId')),
                            fn (Builder $query): Builder => $query->where(
                                'quality_period_id',
                                (int) $get('qualityPeriodId'),
                            ),
                        )
                        ->orderBy('version')
                        ->get()
                        ->mapWithKeys(fn (StandardVersion $version): array => [
                            $version->id => trim($version->version.' — '.($version->standard?->code ?? '')),
                        ])
                        ->all();
                })
                ->searchable()
                ->live(debounce: 300)
                ->visible(fn (Get $get): bool => filled($get('standardSourceId')))
                ->afterStateUpdated(function (Set $set): void {
                    $set('standardId', null);
                }),
            Select::make('standardId')
                ->label('Standar')
                ->placeholder('— Semua Standar —')
                ->options(function (Get $get): array {
                    if (blank($get('standardSourceId'))) {
                        return [];
                    }

                    return Standard::query()
                        ->where('standard_source_id', (int) $get('standardSourceId'))
                        ->when(
                            filled($get('qualityPeriodId')),
                            fn (Builder $query): Builder => $query->whereHas(
                                'standardVersions',
                                fn (Builder $versionQuery): Builder => $versionQuery->where(
                                    'quality_period_id',
                                    (int) $get('qualityPeriodId'),
                                ),
                            ),
                        )
                        ->when(
                            filled($get('standardVersionId')),
                            fn (Builder $query): Builder => $query->whereHas(
                                'standardVersions',
                                fn (Builder $versionQuery): Builder => $versionQuery->whereKey(
                                    (int) $get('standardVersionId'),
                                ),
                            ),
                        )
                        ->orderBy('code')
                        ->get()
                        ->mapWithKeys(fn (Standard $standard): array => [
                            $standard->id => trim($standard->code.' — '.$standard->name),
                        ])
                        ->all();
                })
                ->searchable()
                ->visible(fn (Get $get): bool => filled($get('standardSourceId'))),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function filterPayload(
        ?string $standardSourceId,
        ?string $qualityPeriodId = null,
        ?string $standardVersionId = null,
        ?string $standardId = null,
    ): array {
        return [
            'standard_source_id' => $standardSourceId,
            'quality_period_id' => $qualityPeriodId,
            'standard_version_id' => $standardVersionId,
            'standard_id' => $standardId,
        ];
    }
}
