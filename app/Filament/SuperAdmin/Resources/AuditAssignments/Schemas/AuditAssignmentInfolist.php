<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Schemas;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditAssignmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audit Assignment')
                    ->schema([
                        TextEntry::make('auditorPosition.user.name')->label('Auditor'),
                        TextEntry::make('auditorPosition.position.name')->label('Jabatan'),
                        TextEntry::make('organizationUnit.name')->label('Unit Auditee'),
                        TextEntry::make('assigned_at')->label('Tanggal Penugasan')->dateTime(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Status Auditor Workflow')
                    ->schema([
                        TextEntry::make('workflowInstance.current_status')
                            ->label('Status Saat Ini')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => $state !== null
                                ? (AuditAssignmentResource::workflowStatusLabels()[$state] ?? $state)
                                : '-')
                            ->color(fn (?string $state): string => $state !== null
                                ? (AuditAssignmentResource::workflowStatusColors()[$state] ?? 'gray')
                                : 'gray'),
                        RepeatableEntry::make('workflowInstance.histories')
                            ->label('Riwayat')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => AuditAssignmentResource::workflowStatusLabels()[$state] ?? $state)
                                        ->color(fn (string $state): string => AuditAssignmentResource::workflowStatusColors()[$state] ?? 'gray'),
                                    TextEntry::make('actor.name')->label('Oleh')->placeholder('-'),
                                    TextEntry::make('acted_at')->label('Pada')->dateTime(),
                                    TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
