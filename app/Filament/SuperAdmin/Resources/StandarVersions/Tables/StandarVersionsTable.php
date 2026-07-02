<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StandarVersionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('standard.code')
                    ->label('Kode Standar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qualityPeriod.code')
                    ->label('Kode Periode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version')
                    ->label('Versi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
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
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
