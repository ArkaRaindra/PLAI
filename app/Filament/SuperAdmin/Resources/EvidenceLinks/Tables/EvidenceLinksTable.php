<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Tables;

use App\Models\EvidenceLinks;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvidenceLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_type')
                    ->label('Jenis Referensi')
                    ->badge()
                    ->formatStateUsing(fn (EvidenceLinks $record): string => $record->referenceTypeLabel()),
                TextColumn::make('reference')
                    ->label('Item Terhubung')
                    ->state(fn (EvidenceLinks $record): string => $record->referenceTitle() ?? "#{$record->reference_id} (tidak ditemukan)"),
                TextColumn::make('createdBy.name')
                    ->label('Ditautkan Oleh')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Ditautkan Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions(ActionGroup::make([
                EditAction::make()
                    ->authorize(fn (EvidenceLinks $record): bool => auth()->user()?->can('update', $record) ?? false),
                DeleteAction::make()
                    ->authorize(fn (EvidenceLinks $record): bool => auth()->user()?->can('delete', $record) ?? false),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
