<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Tables;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('workflowInstance.current_status')
                    ->label('Status Workflow')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null
                        ? (AuditAssignmentResource::workflowStatusLabels()[$state] ?? $state)
                        : '-')
                    ->color(fn (?string $state): string => $state !== null
                        ? (AuditAssignmentResource::workflowStatusColors()[$state] ?? 'gray')
                        : 'gray'),
            ])
            ->filters([
                SelectFilter::make('workflow_status')
                    ->label('Status Workflow')
                    ->options(AuditAssignmentResource::workflowStatusLabels())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return $query->whereHas(
                            'workflowInstance',
                            fn (Builder $inner) => $value ? $inner->where('current_status', $value) : $inner,
                        );
                    }),
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
                AuditAssignmentResource::assignAction(),
                AuditAssignmentResource::moveToCorrectiveActionAction(),
                AuditAssignmentResource::startVerificationAction(),
                AuditAssignmentResource::returnToCorrectiveActionAction(),
                AuditAssignmentResource::closeAssignmentAction(),
                DeleteAction::make(),
            ]));
    }
}
