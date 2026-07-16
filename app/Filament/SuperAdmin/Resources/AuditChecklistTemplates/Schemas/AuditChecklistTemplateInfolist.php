<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditChecklistTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')->label('Nama Template'),
                        TextEntry::make('version_no')->label('Versi')->badge(),
                        TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
                    ]),
                
                Section::make('Item Checklist')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('sequence')->label('No.'),
                                TextEntry::make('question')->label('Pertanyaan')->columnspan(2),
                                TextEntry::make('standardVersion.standard.name')->label('Standar'),
                            ])
                            ->columns(4),
                    ])
            ]);
    }
}
