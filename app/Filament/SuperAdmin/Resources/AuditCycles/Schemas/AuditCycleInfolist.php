<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditCycleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audit Cycle')
                    ->schema([
                        TextEntry::make('qualityPeriod.name')->label('Periode       '),
                        TextEntry::make('checklistTemplate.name')->label('Checklist Template'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'draft' => 'Draft',
                                'active' => 'Berjalan',
                                'closed' => 'Ditutup',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'active' => 'success',
                                'closed' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('assignments_count')->label('Jumlah Penugasan')->state(fn ($record) => $record->assignments()->count()),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
