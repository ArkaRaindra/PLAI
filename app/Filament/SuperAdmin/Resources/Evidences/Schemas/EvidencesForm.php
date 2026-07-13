<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Schemas;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EvidencesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::evidenceFormSchema());
    }

    /**
     * @return array<int, Component>
     */
    public static function evidenceFormSchema(): array
    {
        return [
            Select::make('organization_unit_id')
                ->label('Unit Organisasi')
                ->relationship(name: 'organizationUnit', titleAttribute: 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('title')
                ->label('Judul Bukti')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Deskripsi Bukti')
                ->columnSpanFull(),
            Select::make('type')
                ->label('Tipe Bukti')
                ->options([
                    'file' => 'File',
                    'url' => 'URL',
                ])
                ->required()
                ->live()
                ->native(false),
            FileUpload::make('file_path')
                ->label('File Bukti')
                ->disk('local')
                ->directory('evidences')
                ->visibility('private')
                ->visible(fn (Get $get): bool => $get('type') === 'file')
                ->required(fn (Get $get): bool => $get('type') === 'file'),
            TextInput::make('url_path')
                ->label('URL Bukti')
                ->url()
                ->visible(fn (Get $get): bool => $get('type') === 'url')
                ->required(fn (Get $get): bool => $get('type') === 'url'),
        ];
    }
}
