<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Tables;

use App\Support\Filament\TableContextMenu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StandarSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('is_active')
                    ->label('Status')
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
