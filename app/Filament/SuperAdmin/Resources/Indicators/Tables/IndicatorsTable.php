<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Tables;

use App\Enums\CalculationMethod;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IndicatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('parentIndicator'))
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parentIndicator.code')
                    ->label('Induk')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('calculation_method')
                    ->label('Metode Perhitungan')
                    ->formatStateUsing(fn (CalculationMethod|string|null $state): string => $state instanceof CalculationMethod ? $state->getLabel() : ($state ?? ''))
                    ->sortable(),
                TextColumn::make('measurement_unit')
                    ->label('Satuan')
                    ->sortable(),
                TextColumn::make('weight')
                    ->label('Bobot')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
