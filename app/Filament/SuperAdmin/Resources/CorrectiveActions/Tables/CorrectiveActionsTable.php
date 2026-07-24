<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Tables;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CorrectiveActionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('auditFinding.title')
                    ->label('Temuan')
                    ->wrap(),
                TextColumn::make('ownerPosition.user.name')
                    ->label('PIC'),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CorrectiveActionResource::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => CorrectiveActionResource::statusColors()[$state] ?? 'gray'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record): bool => $record->isEditable()),
                CorrectiveActionResource::progressTimelineAction(),
                CorrectiveActionResource::submitAction(),
                DeleteAction::make()
                    ->visible(fn ($record): bool => $record->isEditable()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
