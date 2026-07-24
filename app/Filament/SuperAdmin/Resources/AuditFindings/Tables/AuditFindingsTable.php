<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Tables;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('workflowInstance.current_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null
                        ? (AuditFindingResource::workflowStatusLabels()[$state] ?? $state)
                        : '-')
                    ->color(fn (?string $state): string => $state !== null
                        ? (AuditFindingResource::workflowStatusColors()[$state] ?? 'gray')
                        : 'gray'),
                TextColumn::make('due_date')
                    ->label('Batas Waktu')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
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
                SelectFilter::make('workflow_status')
                    ->label('Status Workflow')
                    ->options(AuditFindingResource::workflowStatusLabels())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return $query;
                        }

                        return $query->whereHas('workflowInstance', fn (Builder $inner) => $inner->where('current_status', $value));
                    }),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                AuditFindingResource::manageCorrectiveActionAction(),
                AuditFindingResource::assignAction(),
                AuditFindingResource::moveToCorrectiveAction(),
                AuditFindingResource::startVerificationAction(),
                AuditFindingResource::returnToCorrectiveActionAction(),
                AuditFindingResource::closeAction(),
                DeleteAction::make(),
            ]));
    }
}
