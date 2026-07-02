<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorMappingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('internalIndicator.name')
                    ->label('Indikator Internal')
                    ->searchable(),
                TextColumn::make('externalIndicator.name')
                    ->label('Indikator Eksternal')
                    ->searchable(),
                TextColumn::make('is_primary')
                    ->label('Data Utama')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state): string => $state ? 'Ya' : 'Bukan'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i:s')
                    ->formatStateUsing(fn ($state): string => Carbon::parse($state)->format('d-M-Y H:i:s'))
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
