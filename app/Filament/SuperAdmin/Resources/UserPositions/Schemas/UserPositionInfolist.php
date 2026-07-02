<?php

namespace App\Filament\SuperAdmin\Resources\UserPositions\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserPositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jabatan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama Pengguna'),
                                TextEntry::make('position.name')
                                    ->label('Jabatan'),
                                TextEntry::make('organizationUnit.name')
                                    ->label('Unit Organisasi'),
                                TextEntry::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->dateTime('d M Y H:i:s'),
                                TextEntry::make('end_date')
                                    ->label('Tanggal Berakhir')
                                    ->dateTime('d M Y H:i:s'),
                                TextEntry::make('is_active')
                                    ->label('Status Jabatan')
                                    ->formatStateUsing(fn ($state): string => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->color(fn ($state): string => $state ? 'success' : 'danger'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Meta Data')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Tanggal Dibuat')
                                    ->formatStateUsing(fn ($state): string => Carbon::parse($state)->format('d M Y H:i:s')),
                                TextEntry::make('updated_at')
                                    ->label('Tanggal Diperbarui')
                                    ->formatStateUsing(fn ($state): string => Carbon::parse($state)->format('d M Y H:i:s')),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
