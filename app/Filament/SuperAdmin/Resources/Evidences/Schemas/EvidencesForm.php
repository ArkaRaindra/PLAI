<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Schemas;

use App\Enums\EvidenceCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvidencesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship(name: 'organizationUnit', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category')
                    ->label('Kategori')
                    ->options(EvidenceCategory::class)
                    ->native(false)
                    ->searchable(),
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}