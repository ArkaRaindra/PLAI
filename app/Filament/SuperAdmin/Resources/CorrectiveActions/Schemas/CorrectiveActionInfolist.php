<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
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
                Section::make('Verification History')
                    ->description('CAPA-003: riwayat setiap transisi status Corrective Action, tersimpan otomatis oleh workflow engine.')
                    ->schema([
                        RepeatableEntry::make('workflowInstance.histories')
                            ->label('Riwayat Verifikasi')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => CorrectiveActionResource::statusLabels()[$state] ?? $state)
                                        ->color(fn (string $state): string => CorrectiveActionResource::statusColors()[$state] ?? 'gray'),
                                    TextEntry::make('actor.name')->label('Oleh')->placeholder('-'),
                                    TextEntry::make('acted_at')->label('Pada')->dateTime(),
                                    TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
