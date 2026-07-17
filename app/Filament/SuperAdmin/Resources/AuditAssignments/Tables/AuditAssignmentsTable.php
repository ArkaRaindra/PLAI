<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Tables;

use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Models\AuditAssignment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('auditorPosition.user.name')
                    ->label('Auditor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Auditee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assigned_at')
                    ->label('Tanggal Penugasan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('responses_count')
                    ->label('Jawaban')
                    ->counts('responses')
                    ->badge(),
                TextColumn::make('findings_count')
                    ->label('Temuan')
                    ->counts('findings')
                    ->badge(),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                Action::make('fillChecklist')
                    ->label('Isi Checklist')
                    ->icon(Heroicon::ChatBubbleLeftRight)
                    ->url(fn (AuditAssignment $record): string => AuditChecklistResponseResource::getListUrl($record->id)),
                 Action::make('manageFindings')
                    ->label('Kelola Temuan')
                    ->icon(Heroicon::ExclamationTriangle)
                    ->url(fn (AuditAssignment $record): string => AuditFindingResource::getListUrl($record->id)),
                DeleteAction::make(),
            ]));
    }
}