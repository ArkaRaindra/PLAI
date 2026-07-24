<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CorrectiveActionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Corrective Action')
                    ->schema([
                        TextEntry::make('auditFinding.title')->label('Temuan')->columnSpanFull(),
                        TextEntry::make('ownerPosition.user.name')->label('PIC'),
                        TextEntry::make('ownerPosition.position.name')->label('Jabatan'),
                        TextEntry::make('due_date')->label('Due Date')->date(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => CorrectiveActionResource::statusLabels()[$state] ?? $state)
                            ->color(fn (string $state): string => CorrectiveActionResource::statusColors()[$state] ?? 'gray'),
                        TextEntry::make('plan')->label('Action Plan')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
