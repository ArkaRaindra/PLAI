<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditChecklistTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Checklist')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
