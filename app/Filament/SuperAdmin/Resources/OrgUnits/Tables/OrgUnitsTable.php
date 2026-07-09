<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Tables;

use App\Support\Filament\TableContextMenu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrgUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent.code')->label('Induk')
                    ->default('-'),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('type')->label('Jenis'),
                TextColumn::make('code')->label('Kode'),
                TextColumn::make('is_active')->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'AKTIF' : 'TIDAK AKTIF')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
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
