<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditChecklistResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('checklistItem.sequence')
                    ->label('No. ')
                    ->sortable(),
                TextColumn::make('checklistItem.question')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->limit(100),
                TextColumn::make('answer')
                    ->label('Jawaban')
                    ->wrap()
                    ->limit(80)
                    ->placeholder('-'),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->wrap()
                    ->limit(80)
                    ->placeholder('-'),
                TextColumn::make('evidence.title')
                    ->label('Evidence')
                    ->placeholder('-'),
            ])
            ->defaultSort('id')
            ->filters([
                //
            ])
            ->recordActions(ActionGroup::make([
                // ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ]));
            // ->toolbarActions([
            //     BulkActionGroup::make([
            //         DeleteBulkAction::make(),
            //     ]),
            // ]);
    }
}
