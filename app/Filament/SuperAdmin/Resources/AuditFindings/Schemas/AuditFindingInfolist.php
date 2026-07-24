<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Schemas;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use Filament\Infolists\Components\RepeatableEntry;
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
                            Section::make('Status Auditor Workflow')
                                ->schema([
                                    TextEntry::make('workflowInstance.current_status')
                                        ->label('Status Saat Ini')
                                        ->badge()
                                        ->formatStateUsing(fn (?string $state): string => $state !== null
                                            ? (AuditFindingResource::workflowStatusLabels()[$state] ?? $state)
                                            : '-')
                                        ->color(fn (?string $state): string => $state !== null
                                            ? (AuditFindingResource::workflowStatusColors()[$state] ?? 'gray')
                                            : 'gray'),
                                ]),
                            RepeatableEntry::make('workflowInstance.histories')
                                ->label('Riwayat Status')
                                ->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => AuditFindingResource::workflowStatusLabels()[$state] ?? $state)
                                        ->color(fn (string $state): string => AuditFindingResource::workflowStatusColors()[$state] ?? 'gray'),
                                    TextEntry::make('actor.name')->label('Oleh')->placeholder('-'),
                                    TextEntry::make('acted_at')->label('Pada')->dateTime(),
                                    TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                                ])
                                ->columns(3),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
