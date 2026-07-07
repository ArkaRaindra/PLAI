<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrgUnitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('parent.code')->label('Induk'),
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('type')->label('Jenis'),
                TextEntry::make('code')->label('Kode'),
                TextEntry::make('is_active')->label('Status')->formatStateUsing(fn ($state) => $state ? 'AKTIF' : 'TIDAK AKTIF')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ]);
    }
}
