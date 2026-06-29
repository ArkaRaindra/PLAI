<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QualityPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'closed' => 'Ditutup',
                    ])
                    ->default('draft')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif'),
            ]);
    }
}
