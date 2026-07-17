<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Tables;

use App\Filament\SuperAdmin\Resources\AuditCycles\Schemas\AuditCycleForm;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditCyclesTable
{
    private const STATUS_COLORS = [
        'draft' => 'gray',
        'ongoing' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('qualityPeriod.name')
                    ->label('Periode Audit')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('checklistTemplate.name')
                    ->label('Checklist Template')
                    ->formatStateUsing(fn ($state, $record): string => "{$record->checklistTemplate?->name} (v{$record->checklistTemplate?->version_no})")
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AuditCycleForm::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => self::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AuditCycleForm::STATUS_LABELS),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}