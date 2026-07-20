<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\RelationManagers;

use App\Models\AuditChecklistTemplateItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditChecklistTemplateItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Checklist';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
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
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->bulkActions([]);
    }
}
