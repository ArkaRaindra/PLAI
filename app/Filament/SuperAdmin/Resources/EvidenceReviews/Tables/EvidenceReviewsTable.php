<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Tables;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\EvidenceReview;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EvidenceReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->Searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Review')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'revision_needed' => 'Perlu Revisi',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'revision_needed' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('review_notes')
                    ->label('Catatan Review')
                    ->limit(50)
                    ->placeholder('-'),
                TextColumn::make('assignedBy.name')
                    ->label('Ditugaskan Oleh')
                    ->placeholder('-'),
                TextColumn::make('assigned_at')
                    ->label('ditugaskan Pada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label('Direview Pada')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Review')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'revision_needed' => 'Perlu Revisi',
                    ]),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make()
                    ->authorize(fn (EvidenceReview $record): bool => auth()->user()?->can('update', $record) ?? false),
                EvidenceReviewResource::approveAction(),
                EvidenceReviewResource::rejectAction(),
                DeleteAction::make()
                    ->authorize(fn (EvidenceReview $record): bool => auth()->user()?->can('delete', $record) ?? false),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
