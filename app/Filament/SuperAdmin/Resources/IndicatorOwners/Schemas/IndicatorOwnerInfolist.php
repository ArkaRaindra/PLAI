<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IndicatorOwnerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kepemilikan Indikator')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('indicator.code')->label('Kode Indikator'),
                            TextEntry::make('indicator.name')->label('Nama Indikator'),
                            TextEntry::make('organizationUnit.code')->label('Kode Unit'),
                            TextEntry::make('organizationUnit.name')->label('Unit Organisasi'),
                            TextEntry::make('userPosition.user.name')->label('Pengguna')->placeholder('—'),
                            TextEntry::make('userPosition.position.name')->label('Jabatan')->placeholder('—'),
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
