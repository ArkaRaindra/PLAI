<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvidenceReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('evidence_id'),
                Select::make('reviewer_id')
                    ->label('Reviewer')
                    ->relationship(
                        name: 'reviewer',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->permission('evidence.review')->where('is_active', true),
                    )
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->required(),
                Textarea::make('review_notes')
                    ->label('Catatan Review')
                    ->helperText('Catatan dapat diisi/diperbarui oleh reviewer selama proses review berlangsung.')
                    ->columnSpanFull(),
            ]);
    }
}
