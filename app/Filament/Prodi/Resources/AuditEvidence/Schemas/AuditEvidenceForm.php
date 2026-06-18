<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Schemas;

use App\Models\Period;
use App\Models\SubStandard;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditEvidenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('standard_id')
                    ->relationship('standard', 'code')
                    ->reactive()
                    ->afterStateUpdated(fn ($set) => $set('sub_standard_ids', null)),
                Select::make('sub_standard_ids')
                    ->options(function (callable $get) {
                        $standardId = $get('standard_id');
                        if (! $standardId) {
                            return SubStandard::pluck('code', 'id');
                        }

                        return SubStandard::where('standard_id', $standardId)
                            ->pluck('code', 'id');
                    })
                    ->multiple()
                    ->required()
                    ->label('Sub Standar'),
                Select::make('period_id')
                    ->relationship('period', 'name')
                    ->default(fn () => Period::where('is_active', true)->first()?->id)
                    ->disabled()
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
                Placeholder::make('status')
                    ->label('Status')
                    ->content(fn ($record) => match ($record?->status) {
                        'draft' => 'Draft',
                        'submitted' => 'Dikirim ke auditor',
                        'approved' => 'Diterima',
                        'returned' => 'Ditolak',
                        default => $record?->status ?? 'Draft',
                    }),
                Placeholder::make('current_score')
                    ->label('Nilai')
                    ->content(fn ($record) => $record?->auditor_score ? number_format($record?->auditor_score, 2, ',', '.') : '-'),
                Placeholder::make('auditor_note')
                    ->label('Note')
                    ->content(fn ($record) => $record?->auditor_note ?? '-')
                    ->columnSpanFull(),
            ]);
    }
}
