<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Tables;

use App\Models\TraceabilityLinks;
use App\Support\Filament\TableContextMenu;
use App\Support\TraceabilityLinks\TraceabilityLinkPresenter;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DataTrailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Jejak Ketertelusuran')
            ->description('Riwayat hubungan antar entitas pada penilaian mandiri yang dipilih.')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['source', 'target'])
                ->orderBy(DB::raw('COALESCE(performed_at, created_at)')))
            ->columns([
                Stack::make([
                    Split::make([
                        IconColumn::make('source_icon')
                            ->icon(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::iconFor($record->source_type))
                            ->color(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::colorFor($record->source_type))
                            ->size(IconSize::Large)
                            ->grow(false),
                        Stack::make([
                            Split::make([
                                TextColumn::make('source_name')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->icon(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::iconFor($record->source_type))
                                    ->iconColor(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::colorFor($record->source_type))
                                    ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::sourceDisplayName($record)),
                                TextColumn::make('source_type_label')
                                    ->badge()
                                    ->alignEnd()
                                    ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::entityTypeLabel($record->source_type))
                                    ->color(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::colorFor($record->source_type)),
                            ]),
                            TextColumn::make('relation_label')
                                ->label('Relasi')
                                ->color('primary')
                                ->size(TextSize::Small)
                                ->weight(FontWeight::Medium)
                                ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::relationLabel($record->relation_type)),
                            TextColumn::make('target_name')
                                ->label('Tujuan')
                                ->color('gray')
                                ->size(TextSize::Small)
                                ->icon('tabler-arrow-down-right')
                                ->state(fn (TraceabilityLinks $record): string => TraceabilityLinkPresenter::targetDisplayName($record)),
                            TextColumn::make('journey')
                                ->label('Alur')
                                ->listWithLineBreaks()
                                ->color('gray')
                                ->size(TextSize::Small)
                                ->state(fn (TraceabilityLinks $record): array => TraceabilityLinkPresenter::journeyLines($record)),
                            TextColumn::make('metadata_summary')
                                ->label('Informasi Tambahan')
                                ->color('gray')
                                ->size(TextSize::ExtraSmall)
                                ->visible(fn (?TraceabilityLinks $record): bool => $record instanceof TraceabilityLinks
                                    && filled(TraceabilityLinkPresenter::formatMetadata($record)))
                                ->state(fn (TraceabilityLinks $record): ?string => TraceabilityLinkPresenter::formatMetadata($record)),
                            TextColumn::make('timeline_at')
                                ->label('Waktu')
                                ->color('gray')
                                ->size(TextSize::ExtraSmall)
                                ->state(fn (TraceabilityLinks $record) => TraceabilityLinkPresenter::timelineAt($record))
                                ->dateTime('d M Y H:i'),
                        ]),
                    ])->from('md'),
                ]),
            ])
            ->defaultGroup(
                Group::make('performed_at')
                    ->label('Tanggal')
                    ->date()
                    ->orderQueryUsing(fn (Builder $query): Builder => $query->orderBy(DB::raw('COALESCE(performed_at, created_at)')))
            )
            ->emptyStateHeading('Belum ada jejak data')
            ->emptyStateDescription('Belum ada jejak ketertelusuran untuk penilaian mandiri yang dipilih.')
            ->contextMenuActions([
                TableContextMenu::view(),
            ])
            ->paginated(false)
            ->asTimeline();
    }
}
