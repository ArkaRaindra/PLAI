<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Schemas;

use App\Models\TraceabilityLinks;
use App\Support\TraceabilityLinks\TraceabilityLinkPresenter;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DataTrailsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan')
                    ->schema([
                        TextEntry::make('source_name')
                            ->label('Entitas Awal')
                            ->icon(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::iconFor($record->source_type))
                            ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::sourceDisplayName($record)),
                        TextEntry::make('source_type_label')
                            ->label('Jenis Entitas Awal')
                            ->badge()
                            ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::entityTypeLabel($record->source_type))
                            ->color(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::colorFor($record->source_type)),
                        TextEntry::make('relation_label')
                            ->label('Relasi')
                            ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::relationLabel($record->relation_type)),
                        TextEntry::make('target_name')
                            ->label('Entitas Akhir')
                            ->icon(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::iconFor($record->target_type))
                            ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::targetDisplayName($record)),
                        TextEntry::make('target_type_label')
                            ->label('Jenis Entitas Akhir')
                            ->badge()
                            ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::entityTypeLabel($record->target_type))
                            ->color(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::colorFor($record->target_type)),
                        TextEntry::make('timeline_at')
                            ->label('Waktu')
                            ->state(fn (TraceabilityLinks $record) => TraceabilityLinkPresenter::timelineAt($record))
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Alur')
                    ->schema([
                        TextEntry::make('journey')
                            ->hiddenLabel()
                            ->listWithLineBreaks()
                            ->state(fn (TraceabilityLinks $record): array => TraceabilityLinkPresenter::journeyLines($record)),
                    ]),
                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('metadata_summary')
                            ->hiddenLabel()
                            ->state(fn (TraceabilityLinks $record): ?string => TraceabilityLinkPresenter::formatMetadata($record))
                            ->placeholder('Tidak ada informasi tambahan'),
                    ])
                    ->visible(fn (?TraceabilityLinks $record): bool => $record instanceof TraceabilityLinks
                        && filled(TraceabilityLinkPresenter::formatMetadata($record))),
            ]);
    }
}
