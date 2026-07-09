<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Tables;

use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Models\Realization;
use App\Support\Filament\TableContextMenu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RealizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('actual_value')
                    ->label('Nilai Aktual')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Skor')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitted' => 'Diajukan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->contextMenuActions([
                TableContextMenu::view(),
                TableContextMenu::edit(EditAction::make()
                    ->authorize(fn (Realization $record): bool => auth()->user()?->can('update', $record) ?? false)),
                TableContextMenu::submit(RealizationResource::submitAction()),
                TableContextMenu::approve(RealizationResource::approveAction()),
                TableContextMenu::reject(RealizationResource::rejectAction()),
                // TableContextMenu::delete(DeleteAction::make()
                //     ->authorize(fn (Realization $record): bool => auth()->user()?->can('delete', $record) ?? false)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
