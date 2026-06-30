<?php

namespace App\Filament\SuperAdmin\Resources\Positions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')->label('Kode'),
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
            ]);
    }
}
