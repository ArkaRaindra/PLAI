<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\Tables;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use App\Models\AuditCycle;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditCyclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('qualityPeriod.name')
                    ->label('Periode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('checklistTemplate.name')
                    ->label('Checklist Template')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'active' => 'Berjalan',
                        'closed' => 'Ditutup',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('assignments_count')
                    ->label('Penugasan')
                    ->counts('assignments')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Berjalan',
                        'closed' => 'Ditutup',
                    ]),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                Action::make('manageAssignments')
                    ->label('Kelola Penugasan')
                    ->icon(Heroicon::UserGroup)
                    ->url(fn (AuditCycle $record): string => AuditAssignmentResource::getListUrl($record->id)),
                AuditCycleResource::activateAction(),
                AuditCycleResource::closeAction(),
                DeleteAction::make(),
            ]));
    }
}