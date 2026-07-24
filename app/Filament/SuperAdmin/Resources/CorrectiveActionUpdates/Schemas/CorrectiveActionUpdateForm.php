<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\Schemas;

use App\Models\CorrectiveActionUpdate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CorrectiveActionUpdateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('corrective_action_id'),
                Section::make('Progress Update')
                    ->schema([
                        TextInput::make('progress_percentage')
                            ->label('Progress (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->required()
                            ->helperText(fn (Get $get): string => filled($get('corrective_action_id'))
                                ? 'Progress terakhir: '.(CorrectiveActionUpdate::query()
                                    ->where('corrective_action_id', $get('corrective_action_id'))
                                    ->orderByDesc('id')
                                    ->value('progress_percentage') ?? 0).'%. Progress tidak dapat dikurangi.'
                                : ''),
                        Textarea::make('description')
                            ->label('Catatan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Bukti Pendukung (Opsional)')
                    ->schema([
                        Select::make('evidence_type')
                            ->label('Tipe Bukti')
                            ->options([
                                'none' => 'Tidak Ada',
                                'file' => 'File',
                                'url' => 'URL',
                            ])
                            ->default('none')
                            ->live()
                            ->native(false),
                        FileUpload::make('evidence_file_path')
                            ->label('File Bukti')
                            ->disk('local')
                            ->directory('corrective-action-updates')
                            ->visibility('private')
                            ->visible(fn (Get $get): bool => $get('evidence_type') === 'file')
                            ->required(fn (Get $get): bool => $get('evidence_type') === 'file'),
                        TextInput::make('evidence_url_path')
                            ->label('URL Bukti')
                            ->url()
                            ->visible(fn (Get $get): bool => $get('evidence_type') === 'url')
                            ->required(fn (Get $get): bool => $get('evidence_type') === 'url'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}