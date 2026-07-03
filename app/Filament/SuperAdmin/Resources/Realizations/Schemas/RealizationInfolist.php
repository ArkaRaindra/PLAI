<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RealizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Realisasi')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('target.indicator.name')->label('Indikator'),
                            TextEntry::make('target.qualityPeriod.code')->label('Periode'),
                            TextEntry::make('organizationUnit.name')->label('Unit Organisasi'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn(string $state): string => match ($state) {
                                    'draft' => 'Draft',
                                    'submitted' => 'Diajukan',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                })
                                ->color(fn(string $state): string => match ($state) {
                                    'draft' => 'gray',
                                    'submitted' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('actual_value')->label('Nilai Aktual')->numeric(decimalPlaces: 2),
                            TextEntry::make('score')->label('Skor')->numeric(decimalPlaces: 2),
                            TextEntry::make('notes')->label('Catatan')->columnSpanFull(),
                        ]),
                    ]),
                RepeatableEntry::make('statusHistories')
                    ->label('Riwayat Status')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn(string $state) => match ($state) {
                                'draft' => 'Draft',
                                'submitted' => 'Diajukan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => $state,
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'gray',
                                'submitted' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('user.name')
                            ->label('Oleh'),

                        TextEntry::make('created_at')
                            ->label('Status diperbarui pada')
                            ->dateTime(),

                        TextEntry::make('note')
                            ->label('Catatan Penolakan')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->status === 'rejected'),
                    ])
                    ->columns(3),
                Section::make('Meta')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('createdBy.name')->label('Dibuat Oleh')->placeholder('-'),
                            TextEntry::make('updatedBy.name')->label('Diperbarui Oleh')->placeholder('-'),
                            TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                            TextEntry::make('updated_at')->label('Diperbarui')->dateTime(),
                        ]),
                    ]),
            ]);
    }
}
