<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditChecklistTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Kode dipakai untuk mengelompokkan versi-versi dari checklist yang sama.'),
                TextInput::make('name')
                    ->label('Nama Checklist')
                    ->required()
                    ->maxLength(255),
                TextInput::make('version')
                    ->label('Versi')
                    ->default('1.0')
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrated(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}