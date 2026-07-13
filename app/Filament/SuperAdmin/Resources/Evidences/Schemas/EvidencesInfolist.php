<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Schemas;

use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                            TextEntry::make('type')
                                ->label('Tipe')
                                ->badge()
                                ->placeholder('-')
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'file' => 'File',
                                    'url' => 'URL',
                                    default => $state ?? '-',
                                }),
                            TextEntry::make('file_path')
                                ->label('File')
                                ->placeholder('-')
                                ->visible(fn (Evidences $record): bool => $record->type === 'file')
                                ->formatStateUsing(function (?string $state, Evidences $record): HtmlString {
                                    if (blank($state)) {
                                        return new HtmlString('-');
                                    }

                                    return new HtmlString(
                                        '<a class="text-primary-600 underline" href="'
                                        .e(route('evidences.download', $record))
                                        .'" target="_blank" rel="noopener">Unduh file</a>'
                                    );
                                }),
                            TextEntry::make('url_path')->label('URL')->placeholder('-'),
                            TextEntry::make('current_version')->label('Versi Saat Ini')->placeholder('-'),
                            TextEntry::make('createdBy.name')->label('Dibuat Oleh')->placeholder('-'),
                            TextEntry::make('description')->label('Deskripsi')->placeholder('-')->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Riwayat Status')
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
                            ->label('Riwayat')
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
