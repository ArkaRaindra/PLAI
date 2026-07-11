<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Schemas;

use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EvidencesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evidence')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('title')->label('Judul'),
                            TextEntry::make('organizationUnit.name')->label('Unit Organisasi'),
                            TextEntry::make('category')->label('Kategori')->badge()->placeholder('-'),
                            TextEntry::make('current_version')->label('Versi Saat Ini')->placeholder('-'),
                            TextEntry::make('createdBy.name')->label('Dibuat Oleh')->placeholder('-'),
                            TextEntry::make('description')->label('Deskripsi')->placeholder('-')->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Status Workflow')
                    ->schema([
                        TextEntry::make('workflowInstance.current_status')
                            ->label('Status Saat Ini')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => $state !== null
                                ? (EvidencesResource::workflowStatusLabels()[$state] ?? $state)
                                : '-')
                            ->color(fn (?string $state): string => $state !== null
                                ? (EvidencesResource::workflowStatusColors()[$state] ?? 'gray')
                                : 'gray'),
                        RepeatableEntry::make('workflowInstance.histories')
                            ->label('Riwayat Workflow')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => EvidencesResource::workflowStatusLabels()[$state] ?? $state)
                                        ->color(fn (string $state): string => EvidencesResource::workflowStatusColors()[$state] ?? 'gray'),
                                    TextEntry::make('actor.name')->label('Oleh')->placeholder('-'),
                                    TextEntry::make('acted_at')->label('Pada')->dateTime(),
                                    TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}