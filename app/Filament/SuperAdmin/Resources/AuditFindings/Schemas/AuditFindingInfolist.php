<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditFindingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Terbuka' : 'Ditutup')
                            ->color(fn (string $state): string => $state === 'open' ? 'warning' : 'success'),
                        TextEntry::make('indicator.name')->label('Indikator Terkait')->placeholder('-'),
                        TextEntry::make('due_date')->label('Batas Waktu')->date()->placeholder('-'),
                        TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                        TextEntry::make('root_cause')->label('Akar Masalah')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('recommendation')->label('Rekomendasi')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
