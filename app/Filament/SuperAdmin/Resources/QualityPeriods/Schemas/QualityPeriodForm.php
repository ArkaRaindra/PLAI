<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QualityPeriodForm
{
    /**
     * @return array<int, mixed>
     */
    public static function fields(string $prefix = ''): array
    {
        return [
            Radio::make($prefix.'is_active')
                ->label('Aktif')
                ->options([
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif',
                ])
                ->formatStateUsing(fn ($state) => (string) (int) $state)
                ->dehydrateStateUsing(fn ($state) => (bool) $state)
                ->default('1')
                ->columnSpanFull(),
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
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::fields());
    }
}
