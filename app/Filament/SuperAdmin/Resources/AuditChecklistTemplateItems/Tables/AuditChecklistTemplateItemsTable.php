<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Tables;

use App\Models\AuditChecklistTemplateItem;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditChecklistTemplateItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sequence')
                    ->label('No.')
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->limit(150),
                TextColumn::make('standardVersion.standard.name')
                    ->label('Standar')
                    ->placeholder('-')
                    ->formatStateUsing(fn (AuditChecklistTemplateItem $record): string => $record->standardVersion !== null
                        ? "{$record->standardVersion->standard?->name} ({$record->standardVersion->version})"
                        : '-'),
            ])
            ->defaultSort('sequence')
            ->reorderable('sequence')
            ->recordActions(ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
            ]));
    }
}