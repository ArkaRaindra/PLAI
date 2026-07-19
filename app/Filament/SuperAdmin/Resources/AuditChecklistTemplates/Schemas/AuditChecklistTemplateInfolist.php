<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditChecklistTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Checklist Template')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')->label('Nama'),
                            TextEntry::make('version_no')->label('Versi'),
                        ]),
                    ]),
            ]);
    }
}
