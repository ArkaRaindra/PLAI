<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Tables;

use App\Models\AuditChecklistTemplate;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditChecklistTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version_no')
                    ->label('Versi')
                    ->badge()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items'),
                IconColumn::make('is_active_version')
                    ->label('Aktif')
                    ->boolean()
                    ->state(fn (AuditChecklistTemplate $record): bool => $record->isActiveVersion()),
                TextColumn::make('created_at')
                    ->label('Dibust')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->recordActions(ActionGroup::make([
                Action::make('newVersion')
                    ->label('Buat Versi Baru')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Salinan versi baru akan dibuat dari seluruh item checklist versi ini')
                    ->visible(fn (AuditChecklistTemplate $record): bool => $record->isActiveVersion())
                    ->action(function (AuditChecklistTemplate $record): void {
                        $newVersion = AuditChecklistTemplate::createNewVersion($record);

                        Notification::make()
                            ->title("Versi {$newVersion->version_no} dibuat")
                            ->success()
                            ->send();
                    }),
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
