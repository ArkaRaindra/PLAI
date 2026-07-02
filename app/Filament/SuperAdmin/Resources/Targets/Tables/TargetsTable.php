<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TargetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('indicator.code')
                    ->label('Kode Indikator')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('indicator.name')
                    ->label('Nama Indikator')
                    ->searchable(),
                TextColumn::make('qualityPeriod.code')
                    ->label('Periode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target_value')
                    ->label('Target')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
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
