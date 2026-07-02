<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IndicatorMappingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pemetaan Indikator')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('internalIndicator.code')->label('Kode Internal')->placeholder('—'),
                            TextEntry::make('internalIndicator.name')->label('Indikator Internal')->placeholder('—'),
                            TextEntry::make('externalIndicator.code')->label('Kode Eksternal')->placeholder('—'),
                            TextEntry::make('externalIndicator.name')->label('Indikator Eksternal')->placeholder('—'),
                            TextEntry::make('is_primary')->label('Data Utama')->badge()
                                ->formatStateUsing(fn ($state): string => $state ? 'Ya' : 'Bukan'),
                        ]),
                        TextEntry::make('notes')->label('Catatan')->columnSpanFull(),
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
