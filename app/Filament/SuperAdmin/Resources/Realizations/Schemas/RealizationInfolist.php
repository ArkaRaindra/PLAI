<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Schemas;

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
                                    default => 'gray',
                                }),
                            TextEntry::make('actual_value')->label('Nilai Aktual')->numeric(decimalPlaces: 2),
                            TextEntry::make('score')->label('Skor')->numeric(decimalPlaces: 2),
                            TextEntry::make('notes')->label('Catatan')->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Riwayat Status')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('submittedBy.name')->label('Diajukan Oleh')->placeholder('-'),
                            TextEntry::make('submitted_at')->label('Diajukan Pada')->dateTime()->placeholder('-'),
                            TextEntry::make('approvedBy.name')->label('Disetujui Oleh')->placeholder('-'),
                            TextEntry::make('approved_at')->label('Disetujui Pada')->dateTime()->placeholder('-'),
                            TextEntry::make('rejectedBy.name')->label('Ditolak Oleh')->placeholder('-'),
                            TextEntry::make('rejected_at')->label('Ditolak Pada')->dateTime()->placeholder('-'),
                        ]),
                    ]),
                Section::make('Meta')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_by')->label('Dibuat Oleh'),
                            TextEntry::make('updated_by')->label('Diperbarui Oleh'),
                            TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                            TextEntry::make('updated_at')->label('Diperbarui')->dateTime(),
                        ]),
                    ]),
            ]);
    }
}
