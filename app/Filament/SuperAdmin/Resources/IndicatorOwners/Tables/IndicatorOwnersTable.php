<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Tables;

use App\Support\Filament\TableContextMenu;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorOwnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('indicator.name')
                    ->label('Indikator')
                    ->searchable(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable(),
                TextColumn::make('userPosition.user.name')
                    ->label('Nama Pengguna')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('userPosition.position.name')
                    ->label('Jabatan')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('is_primary')
                    ->label('Data Utama')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state): string => $state ? 'Ya' : 'Bukan'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i:s')
                    ->formatStateUsing(fn ($state): string => Carbon::parse($state)->format('d M y H:i:s'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->contextMenuActions([
                TableContextMenu::view(),
                TableContextMenu::edit(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
