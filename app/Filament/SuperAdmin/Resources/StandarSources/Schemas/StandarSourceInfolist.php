<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StandarSourceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sumber Standar')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('code')->label('Kode'),
                            TextEntry::make('name')->label('Nama'),
                            IconEntry::make('is_active')->label('Aktif')->boolean(),
                        ]),
                        TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
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
