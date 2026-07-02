<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TargetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Target')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('indicator.code')->label('Kode Indikator'),
                            TextEntry::make('indicator.name')->label('Nama Indikator'),
                            TextEntry::make('qualityPeriod.code')->label('Periode'),
                            TextEntry::make('target_value')->label('Target')->numeric(decimalPlaces: 2),
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
