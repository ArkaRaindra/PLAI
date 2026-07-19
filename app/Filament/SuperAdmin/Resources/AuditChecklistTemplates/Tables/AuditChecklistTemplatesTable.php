<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Tables;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use App\Models\AuditChecklistTemplate;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditChecklistTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version_no')
                    ->label('Versi')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                Action::make('manageItems')
                    ->label('Kelola Item')
                    ->icon(Heroicon::ListBullet)
                    ->url(fn (AuditChecklistTemplate $record): string => AuditChecklistTemplateItemResource::getListUrl($record->id)),
                AuditChecklistTemplateResource::newVersionAction(),
                DeleteAction::make(),
            ]));
    }
}
