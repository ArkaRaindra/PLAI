<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EvidenceReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Evidence')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('evidence.title')->label('Evidence'),
                                TextEntry::make('reviewer.name')->label('Reviewer'),
                                TextEntry::make('status')
                                    ->label('Status Review')
                                    ->badge()
                                    ->formatStateUsing(fn(String $state): string => match ($state) {
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'revision_needed' => 'Perlu Revisi',
                                        default => $state,
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'revision_needed' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('assignedBy.name')->label('Ditugaskan Oleh')->placeholder('-'),
                                TextEntry::make('assigned_at')->label('Ditugaskan Pada')->dateTime()->placeholder('-'),
                                TextEntry::make('reviewed_at')->label('Direview Pada')->dateTime()->placeholder('-'),
                                TextEntry::make('review_notes')->label('Catatan Review')->placeholder('-')->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Meta')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('createdBy.name')->label('Dibuat Oleh')->placeholder('-'),
                                TextEntry::make('updatedBy.name')->label('Diperbarui Oleh')->placeholder('-'),
                                TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                                TextEntry::make('updated_at')->label('Diperbarui')->dateTime(),
                            ])
                    ])
            ]);
    }
}
