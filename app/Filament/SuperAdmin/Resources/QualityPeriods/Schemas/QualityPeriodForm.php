<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QualityPeriodForm
{
    /**
     * @return array<int, Component>
     */
    public static function fields(string $prefix = ''): array
    {
        return [
            TextInput::make($prefix.'code')
                ->label('Kode')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make($prefix.'name')
                ->label('Nama')
                ->required()
                ->maxLength(255),
            DatePicker::make($prefix.'start_date')
                ->label('Tanggal Mulai')
                ->required()
                ->default(now())
                ->native(false),
            DatePicker::make($prefix.'end_date')
                ->label('Tanggal Selesai')
                ->required()
                ->native(false),
            Select::make($prefix.'status')
                ->label('Status')
                ->options([
                    'draft' => 'Draft',
                    'active' => 'Aktif',
                    'closed' => 'Ditutup',
                ])
                ->default('draft')
                ->required(),
            Toggle::make($prefix.'is_active')
                ->label('Aktif')
                ->default(true),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::fields());
    }
}
