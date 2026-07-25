<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Schemas;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditFindingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            Section::make('Audit Finding')
                                ->schema([
                                    TextEntry::make('title')->label('Judul')->columnSpanFull(),
                                    TextEntry::make('category')
                                        ->label('Kategori')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'kts' => 'KTS (Ketidaksesuaian)',
                                            'ofi' => 'OFI (Opportunity for Improvement)',
                                            'observation' => 'Observation',
                                            default => $state,
                                        }),
                                    TextEntry::make('severity')
                                        ->label('Severity')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                                        ->color(fn (string $state): string => $state === 'major' ? 'danger' : 'warning'),
                                    TextEntry::make('indicator.name')->label('Indikator Terkait')->placeholder('-'),
                                    TextEntry::make('due_date')->label('Batas Waktu')->date()->placeholder('-'),
                                    TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                                    TextEntry::make('root_cause')->label('Akar Masalah')->placeholder('-')->columnSpanFull(),
                                    TextEntry::make('recommendation')->label('Rekomendasi')->placeholder('-')->columnSpanFull(),
                                ]),
                        ])->columnSpan(1),

                        Group::make([
                            Section::make('Status Temuan')
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status Temuan')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Terbuka' : 'Ditutup')
                                        ->color(fn (string $state): string => $state === 'open' ? 'warning' : 'success'),
                                ]),
                            Section::make('Corrective Action')
                                ->schema([
                                    TextEntry::make('correctiveAction.status')
                                        ->label('Status CAPA')
                                        ->badge()
                                        ->placeholder('Belum ada Corrective Action')
                                        ->formatStateUsing(fn (?string $state): string => $state !== null
                                            ? (CorrectiveActionResource::statusLabels()[$state] ?? $state)
                                            : '-')
                                        ->color(fn (?string $state): string => $state !== null
                                            ? (CorrectiveActionResource::statusColors()[$state] ?? 'gray')
                                            : 'gray'),
                                    TextEntry::make('correctiveAction.ownerPosition.user.name')
                                        ->label('PIC')
                                        ->placeholder('-'),
                                    TextEntry::make('correctiveAction.due_date')
                                        ->label('Due Date')
                                        ->date()
                                        ->placeholder('-'),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
