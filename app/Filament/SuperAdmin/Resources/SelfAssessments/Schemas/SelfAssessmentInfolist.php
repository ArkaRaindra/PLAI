<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SelfAssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            Section::make('Self Assessment')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('organizationUnit.name')->label('Unit Organisasi'),
                                        TextEntry::make('qualityPeriod.name')->label('Periode'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'draft' => 'Draft',
                                                'submitted' => 'Diajukan',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                default => $state,
                                            })
                                            ->color(fn (string $state): string => match ($state) {
                                                'draft' => 'gray',
                                                'submitted' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'default' => 'gray',
                                            }),
                                        TextEntry::make('final_score')
                                            ->label('Final Score')
                                            ->numeric(decimalPlaces: 2)
                                            ->placeholder('Belum dihitung'),
                                        TextEntry::make('summary')
                                            ->label('Ringkasan')
                                            ->columnSpanFull(),
                                        TextEntry::make('note_rejected')
                                            ->label('Catatan Penolakan')
                                            ->placeholder('-')
                                            ->columnSpanFull()
                                            ->visible(fn ($record) => $record->status === 'rejected'),
                                    ]),
                                ]),
                            Section::make('Meta')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('createdBy.name')->label('Dibuat oleh')->placeholder('-'),
                                        TextEntry::make('created_at')->label('Dibuat pada')->dateTime()->placeholder('-'),
                                        TextEntry::make('submittedBy.name')->label('Diajukan oleh')->placeholder('-'),
                                        TextEntry::make('submitted_at')->label('Diajukan pada')->dateTime()->placeholder('-')->color('info'),
                                        TextEntry::make('approvedBy.name')->label('Disetujui oleh')->placeholder('-'),
                                        TextEntry::make('approved_at')->label('Disetujui pada')->dateTime()->placeholder('-')->color('success'),
                                        TextEntry::make('rejectedBy.name')->label('Ditolak oleh')->placeholder('-'),
                                        TextEntry::make('rejected_at')->label('Ditolak pada')->dateTime()->placeholder('-')->color('danger'),
                                    ]),
                                ]),
                        ])->columnSpan(1),

                        Group::make([
                            Section::make('Penilaian per Realisasi')
                                ->schema([
                                    RepeatableEntry::make('details')
                                        ->label('')
                                        ->schema([
                                            TextEntry::make('realization.target.indicator.code')->label('Kode Indikator')->badge(),
                                            TextEntry::make('realization.target.indicator.name')->label('Indikator'),
                                            TextEntry::make('realization.target.target_value')->label('Nilai Target')->numeric(decimalPlaces: 2),
                                            TextEntry::make('realization.actual_value')->label('Nilai Realisasi')->numeric(decimalPlaces: 2),
                                            TextEntry::make('realization_percentage')
                                                ->label('Persentase Realisasi')
                                                ->state(fn ($record): float => SelfAssessmentInfolist::realizationPercentage($record))
                                                ->numeric(decimalPlaces: 2)
                                                ->suffix('%'),
                                            TextEntry::make('analysis')->label('Analisis')->placeholder('-')->columnSpanFull(),
                                            TextEntry::make('strength')->label('Kekuatan')->placeholder('-')->columnSpanFull(),
                                            TextEntry::make('weakness')->label('Kelemahan')->placeholder('-')->columnSpanFull(),
                                        ])
                                        ->columns(3),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    private static function realizationPercentage($detail): float
    {
        $realization = $detail?->realization;

        if (! $realization || $realization->target === null || (float) $realization->target->target_value === 0.0) {
            return 0.0;
        }

        return ((float) $realization->actual_value / (float) $realization->target->target_value) * 100;
    }
}
