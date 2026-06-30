<?php

namespace App\Filament\SuperAdmin\Resources\StandarSources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StandarSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('code')->label('Kode'),
                TextColumn::make('is_active')
                ->label('Status')
                ->formatStateUsing(fn($state) => $state ? 'AKTIF' : 'TIDAK AKTIF')
                ->badge()
                ->color(fn($state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->color('info'),
                EditAction::make()->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
