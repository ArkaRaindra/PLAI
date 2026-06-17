<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditEvidenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('title')
                    ->label('Judul')
                    ->content(fn ($record) => $record->title),
                Placeholder::make('sub_standard_id')
                    ->label('Sub Standar'),
                Placeholder::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->content(fn ($record) => $record->description)
                    ->html(),
                Placeholder::make('file_path')
                    ->label('file')
                    ->content(fn ($record) => $record->file_path
                     ? "<a href='".asset('storage/'.$record->file_path)."' target='_blank'> Download</a>"
                     : 'Tidak ada')
                    ->html(),
                Placeholder::make('google_drive_link')
                    ->label('Google Drive')
                    ->content(fn ($record) => $record->google_drive_link
                     ? "a href='{$record->google_drive_link}' target='_blank'> Buka di Drive</a>"
                     : 'Tidak ada')
                    ->html(),
                TextInput::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(4)
                    ->required(),
                Textarea::make('auditor_note')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'approved' => 'Diterima',
                        'returned' => 'Ditolak',
                    ])
                    ->required(),
            ]);
    }
}
