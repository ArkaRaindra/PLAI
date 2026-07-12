<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas;

use App\Models\OrganizationUnit;
use App\Models\QualityPeriod;
use App\Models\Realization;
use App\Models\SelfAssessment;
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
                                )
                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                    $qualityPeriodId = $get('quality_period_id');

                                    if ($state === null || $qualityPeriodId === null) {
                                        return;
                                    }

                                    $realizations = Realization::query()
                                        ->with('target.indicator')
                                        ->whereHas('target', fn ($q) => $q->where('quality_period_id', $qualityPeriodId))
                                        ->where('organization_unit_id', $state)
                                        ->where('status', 'approved')
                                        ->whereDoesntHave('selfAssessmentDetail')
                                        ->orderByDesc('id')
                                        ->get();

                                    $items = $realizations->map(function (Realization $record): array {
                                        return [
                                            'realization_id' => $record->id,
                                            'score' => $record->score,
                                            'analysis' => null,
                                            'strength' => null,
                                            'weakness' => null,
                                        ];
                                    })->all();

                                    $set('details', $items);
                                }),
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
                                        return 'Akan dihitung otomatis dari rata-rata skor realisasi.';
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
                                Hidden::make('realization_id'),
                                Placeholder::make('realisasi')
                                    ->label('Realisasi')
                                    ->content(fn (Get $get): string => self::resolveRealizationLabel($get('realization_id'))),
                                TextInput::make('score')
                                    ->label('Skor')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->readOnly(),
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
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['realization_id'] ?? null
                                ? 'Realisasi #'.$state['realization_id']
                                : 'Realisasi Baru'),
                    ]),
            ]);
    }

    private static function resolveRealizationLabel(?int $realizationId): string
    {
        if ($realizationId === null) {
            return '';
        }

        $record = Realization::query()
            ->with('target.indicator')
            ->find($realizationId);

        if (! $record) {
            return '';
        }

        $target = $record->target;
        $indicator = $target?->indicator;

        return "Realisasi #{$record->id} — [{$indicator?->code}] {$indicator?->name} — Target: {$target?->target_value}";
    }
}
