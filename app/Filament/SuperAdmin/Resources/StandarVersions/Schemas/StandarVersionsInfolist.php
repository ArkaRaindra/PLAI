<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StandarVersionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Versi Standar')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('standard.code')->label('Kode Standar'),
                            TextEntry::make('standard.name')->label('Nama Standar'),
                            TextEntry::make('qualityPeriod.code')->label('Periode'),
                            TextEntry::make('qualityPeriod.name')->label('Nama Periode'),
                            TextEntry::make('version')->label('Versi'),
                            TextEntry::make('status')->label('Status')->badge(),
                            IconEntry::make('is_active')->label('Aktif')->boolean(),
                            TextEntry::make('start_date')->label('Tanggal Mulai')->date(),
                            TextEntry::make('end_date')->label('Tanggal Selesai')->date(),
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
