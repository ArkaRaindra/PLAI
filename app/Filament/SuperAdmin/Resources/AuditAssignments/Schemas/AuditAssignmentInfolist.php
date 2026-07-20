<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Schemas;

use Filament\Infolists\Components\TextEntry;
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
            ]);
    }
}
