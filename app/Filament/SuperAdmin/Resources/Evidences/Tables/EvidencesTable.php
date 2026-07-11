<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Tables;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\Evidences;
use App\Support\Filament\TableContextMenu;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvidencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('current_version')
                    ->label('Versi Saat Ini')
                    ->placeholder('-'),
                TextColumn::make('evidenceReviews_count')
                    ->label('Review')
                    ->state(fn (Evidences $record): int => $record->evidenceReviews()->count())
                    ->badge(),
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
                TableContextMenu::edit(),
                TableContextMenu::urlAction(
                    Action::make('manageReviews')
                        ->label('Kelola Review')
                        ->icon(Heroicon::ClipboardDocumentCheck)
                        ->url(fn (Evidences $record): string => EvidenceReviewResource::getListUrl($record->id)),
                    \LaraZeus\Tabler\Tabler::ClipboardCheck,
                    'info',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}