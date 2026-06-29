<?php

namespace App\Filament\SuperAdmin\Resources\OrgUnits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                ->formatStateUsing(fn($state) => $state ? 'AKTIF' : 'TIDAK AKTIF')
                ->badge()
                ->color(fn($state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
