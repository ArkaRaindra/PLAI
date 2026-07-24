<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Tables;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\EvidenceReview;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EvidenceReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('evidence.title')
                    ->label('Evidence')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('evidence.organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Review')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Menunggu Review',
                        'review' => 'Direview',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'gray',
                        'review' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('review_notes')
                    ->label('Catatan Review')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reviewed_at')
                    ->label('Direview Pada')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Review')
                    ->options([
                        'pending' => 'Menunggu Review',
                        'review' => 'Direview',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(
                fn (EvidenceReview $record): string => EvidenceReviewResource::getUrl(
                    'view',
                    ['record' => $record],
                ),
            );
    }
}
