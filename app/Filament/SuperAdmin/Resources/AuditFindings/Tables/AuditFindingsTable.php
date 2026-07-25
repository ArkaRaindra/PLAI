<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Tables;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditFindingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'kts' => 'KTS',
                        'ofi' => 'OFI',
                        'observation' => 'Observation',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'kts' => 'danger',
                        'ofi' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => $state === 'major' ? 'danger' : 'warning'),
                TextColumn::make('due_date')
                    ->label('Batas Waktu')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('correctiveAction.status')
                    ->label('Corrective Action')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null
                        ? (CorrectiveActionResource::statusLabels()[$state] ?? $state)
                        : 'Belum Ada')
                    ->color(fn (?string $state): string => $state !== null
                        ? (CorrectiveActionResource::statusColors()[$state] ?? 'gray')
                        : 'danger'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'kts' => 'KTS',
                        'ofi' => 'OFI',
                        'observation' => 'Observation',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Terbuka',
                        'closed' => 'Ditutup',
                    ]),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                AuditFindingResource::manageCorrectiveActionAction(),
                AuditFindingResource::closeAction(),
                DeleteAction::make(),
            ]));
    }
}
