<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\Target;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class SelfAssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 12])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)->columnSpan(5)->schema([
                            Select::make('quality_period_id')
                                ->label('Periode Penilaian')
                                ->relationship(
                                    name: 'qualityPeriod',
                                    titleAttribute: 'name'
                                )
                                ->getOptionLabelFromRecordUsing(
                                    fn (QualityPeriod $record): string => "{$record->code} — {$record->name}",
                                )
                                ->searchable(['code', 'name'])
                                ->preload()
                                ->required()
                                ->live(),
                            Select::make('organization_unit_id')
                                ->label('Unit Organisasi')
                                ->relationship(
                                    name: 'organizationUnit',
                                    titleAttribute: 'name'
                                )
                                ->getOptionLabelFromRecordUsing(
                                    fn (OrganizationUnit $record): string => "{$record->code} — {$record->name}",
                                )
                                ->searchable(['code', 'name'])
                                ->preload()
                                ->required()
                                ->live()
                                ->unique(
                                    modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule
                                        ->where('quality_period_id', $get('quality_period_id')),
                                    ignoreRecord: true,
                                ),
                            Hidden::make('status')
                                ->default('draft')
                                ->dehydrated(fn (string $operation): bool => $operation === 'create'),
                            Textarea::make('summary')
                                ->label('Ringkasan')
                                ->columnSpanFull(),
                            Textarea::make('note_rejected')
                                ->label('Catatan Penolakan')
                                ->visible(fn (Get $get): bool => $get('status') === 'rejected')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpanFull(),
                            Placeholder::make('final_score_display')
                                ->label('Final Score')
                                ->content(function (?SelfAssessment $record): string {
                                    if ($record?->final_score === null) {
                                        return 'Akan dihitung otomatis dari rata-rata skor indikator.';
                                    }

                                    return number_format((float) $record->final_score, 2);
                                })
                                ->columnSpanFull(),
                        ]),
                        Repeater::make('details')
                            ->label('Penilaian per Realisasi')
                            ->columnSpan(7)
                            ->relationship('details')
                            ->schema([
                                Select::make('indicator_id')
                                    ->label('Indikator')
                                    ->dehydrated(false)
                                    ->options(function (Get $get): array {
                                        $organizationUnitId = $get('../../organization_unit_id');
                                        $qualityPeriodId = $get('../../quality_period_id');

                                        if ($organizationUnitId === null || $qualityPeriodId === null) {
                                            return [];
                                        }

                                        return Indicator::query()
                                            ->whereHas('targets', fn ($q) => $q->where('quality_period_id', $qualityPeriodId))
                                            ->whereHas('targets.realizations', fn ($q) => $q->where('organization_unit_id', $organizationUnitId))
                                            ->orderBy('code')
                                            ->get()
                                            ->mapWithKeys(fn (Indicator $record): array => [$record->id => "[{$record->code}] — {$record->name}"])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('target_id', null))
                                    ->required(),
                                Select::make('target_id')
                                    ->label('Target')
                                    ->dehydrated(false)
                                    ->options(function (Get $get): array {
                                        $indicatorId = $get('indicator_id');
                                        $organizationUnitId = $get('../../organization_unit_id');
                                        $qualityPeriodId = $get('../../quality_period_id');

                                        if ($indicatorId === null || $organizationUnitId === null || $qualityPeriodId === null) {
                                            return [];
                                        }

                                        return Target::query()
                                            ->where('indicator_id', $indicatorId)
                                            ->where('quality_period_id', $qualityPeriodId)
                                            ->whereHas('realizations', fn ($q) => $q->where('organization_unit_id', $organizationUnitId))
                                            ->orderByDesc('id')
                                            ->get()
                                            ->mapWithKeys(fn (Target $record): array => [$record->id => "Target: {$record->target_value} ({$record->qualityPeriod->code})"])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('realization_id', null))
                                    ->required(),
                                Select::make('realization_id')
                                    ->label('Realisasi')
                                    ->options(function (Get $get): array {
                                        $targetId = $get('target_id');
                                        $organizationUnitId = $get('../../organization_unit_id');

                                        if ($targetId === null || $organizationUnitId === null) {
                                            return [];
                                        }

                                        return Realization::query()
                                            ->with('target.indicator')
                                            ->where('target_id', $targetId)
                                            ->where('organization_unit_id', $organizationUnitId)
                                            ->whereDoesntHave('selfAssessmentDetail')
                                            ->orderByDesc('id')
                                            ->limit(100)
                                            ->get()
                                            ->mapWithKeys(function (Realization $record): array {
                                                $indicator = $record->target?->indicator;

                                                return [$record->id => "Realisasi #{$record->id} (Skor: {$record->score})"];
                                            })
                                            ->all();
                                    })
                                    ->getSearchResultsUsing(function (string $search, Get $get): array {
                                        $targetId = $get('target_id');
                                        $organizationUnitId = $get('../../organization_unit_id');

                                        if ($targetId === null || $organizationUnitId === null) {
                                            return [];
                                        }

                                        return Realization::query()
                                            ->with('target.indicator')
                                            ->where('target_id', $targetId)
                                            ->where('organization_unit_id', $organizationUnitId)
                                            ->whereDoesntHave('selfAssessmentDetail')
                                            ->where('id', 'like', "%{$search}%")
                                            ->orderByDesc('id')
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(function (Realization $record): array {
                                                $indicator = $record->target?->indicator;

                                                return [$record->id => "Realisasi #{$record->id} (Skor: {$record->score})"];
                                            })
                                            ->all();
                                    })
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        if ($value === null) {
                                            return null;
                                        }

                                        $record = Realization::query()
                                            ->with('target.indicator')
                                            ->find($value);

                                        if (! $record) {
                                            return null;
                                        }

                                        return "Realisasi #{$record->id} (Skor: {$record->score})";
                                    })
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('score')
                                    ->label('Skor')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01),
                                Textarea::make('analysis')
                                    ->label('Analisis')
                                    ->columnSpanFull(),
                                Textarea::make('strength')
                                    ->label('Kekuatan')
                                    ->columnSpanFull(),
                                Textarea::make('weakness')
                                    ->label('Kelemahan')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Realisasi')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['realization_id'] ?? null
                                ? 'Realisasi #'.$state['realization_id']
                                : 'Realisasi Baru'),
                    ]),
            ]);
    }
}
