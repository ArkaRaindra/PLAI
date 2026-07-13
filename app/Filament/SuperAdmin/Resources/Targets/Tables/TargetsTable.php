<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Tables;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TargetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('indicator.name')
                    ->label('Nama Indikator')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('indicator.standardVersion.version')
                    ->label('Versi Standar')
                    ->searchable(),
                TextColumn::make('indicator.standardVersion.standard.name')
                    ->label('Nama Standar')
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
            ->recordActions(ActionGroup::make([
                Action::make('realization')
                    ->label('Realisasi')
                    ->icon(Heroicon::ClipboardDocumentCheck)
                    ->color('primary')
                    ->url(fn (Target $record): string => RealizationResource::getListUrl($record->id)),
                ViewAction::make(),
                EditAction::make(),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
