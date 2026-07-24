<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditFindingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('audit_assignment_id'),
                TextInput::make('title')
                    ->label('Judul Temuan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'kts' => 'KTS (Ketidaksesuaian)',
                        'ofi' => 'OFI (Opportunity for Improvement)',
                        'observation' => 'Observation',
                    ])
                    ->native(false)
                    ->required(),
                Select::make('severity')
                    ->label('Severity')
                    ->options([
                        'major' => 'Major',
                        'minor' => 'Minor',
                    ])
                    ->native(false)
                    ->required(),
                Select::make('indicator_id')
                    ->label('Indikator Terkait')
                    ->relationship(name: 'indicator', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('due_date')
                    ->label('Batas Waktu Tindak Lanjut'),
                Textarea::make('description')
                    ->label('Deskripsi Temuan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('root_cause')
                    ->label('Akar Masalah')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('recommendation')
                    ->label('Rekomendasi')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
