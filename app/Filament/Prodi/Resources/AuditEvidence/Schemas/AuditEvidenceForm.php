<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Schemas;

use App\Models\Period;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
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
                Select::make('sub_standard_id')
                    ->relationship('subStandard', 'indicator')
                    ->required()
                    ->label('Sub Standar'),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->default(fn() => Period::where('is_active', true)->first()?->id)
                    ->required()
                    ->label('Periode'),
                TextInput::make('title')
                    ->maxLength(255)
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull()
                    ->label('Deskripsi'),
                FileUpload::make('file_path')
                    ->directory('evidences')
                    ->label('Upload File'),
                TextInput::make('google_drive_link')
                    ->url()
                    ->regex('/^https:\/\/drive\.google\.com\/.*$/')
                    ->label('Link Google Drive'),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ])
                    ->default('draft')
                    ->required()
                    ->disabled(fn($record) => $record && $record->status === 'submitted' ),
                Placeholder::make('current_score')
                    ->label('Nilai')
                    ->content(fn($record) => $record?->scores->first()?->score ?? '-'),
                Placeholder::make('auditor_note')
                    ->label('Note')
                    ->content(fn($record) => $record?->auditor_note ?? '-')
                    ->columnSpanFull(),
            ]);
    }
}
