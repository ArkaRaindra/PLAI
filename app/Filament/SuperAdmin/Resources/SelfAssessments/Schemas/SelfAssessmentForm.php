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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Validation\Rules\Unique;

class SelfAssessmentForm
{
    /**
     * @return array<int, mixed>
     */
    public static function schema(): array
    {
        return [
            Grid::make(['default' => 1, 'lg' => 12])
                ->columnSpanFull()
                ->schema([
                    Section::make('Informasi Penilaian')
                        ->description('Pilih periode dan unit organisasi untuk memuat realisasi yang telah disetujui.')
                        ->columnSpan(5)
                        ->schema([
                            Select::make('quality_period_id')
                                ->label('Periode Penilaian')
                                ->relationship(
                                    name: 'qualityPeriod',
                                    titleAttribute: 'name'
                                )
                                ->getOptionLabelFromRecordUsing(
                                    fn (QualityPeriod $record): string => "{$record->name}",
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
                                    fn (OrganizationUnit $record): string => "{$record->name}",
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
                    Section::make('Penilaian per Realisasi')
                        ->description('Isi analisis, kekuatan, dan kelemahan untuk setiap realisasi yang dimuat.')
                        ->columnSpan(7)
                        ->schema([
                            Repeater::make('details')
                                ->label('Penilaian per Realisasi')
                                ->hiddenLabel()
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
                                    ? 'Realisasi '.self::resolveRealizationPercentage($state['realization_id'])
                                    : 'Realisasi Baru'),
                        ]),
                ]),
        ];
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

        return 'Realisasi '.self::resolveRealizationPercentage($record->id).' — ['.$indicator?->code.'] '.$indicator?->name.' — Target: '.$target?->target_value;
    }

    private static function resolveRealizationPercentage(?int $realizationId): string
    {
        if ($realizationId === null) {
            return '0,00%';
        }

        $record = Realization::query()
            ->with('target')
            ->find($realizationId);

        if (! $record || $record->target === null || (float) $record->target->target_value === 0.0) {
            return '0,00%';
        }

        $percentage = ((float) $record->actual_value / (float) $record->target->target_value) * 100;

        return number_format($percentage, 2).'%';
    }
}
