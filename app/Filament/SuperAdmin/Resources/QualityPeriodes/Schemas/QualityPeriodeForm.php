<?php

namespace App\Filament\SuperAdmin\Resources\QualityPeriodes\Schemas;

use App\Enums\QualityPeriodStatus;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QualityPeriodeForm
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
                ->maxLength(50),
            TextInput::make($prefix.'name')
                ->label('Nama')
                ->required()
                ->maxLength(255),
            DatePicker::make($prefix.'start_date')
                ->label('Tanggal Mulai')
                ->required()
                ->native(false),
            DatePicker::make($prefix.'end_date')
                ->label('Tanggal Selesai')
                ->required()
                ->native(false),
            Select::make($prefix.'status')
                ->label('Status')
                ->options(collect(QualityPeriodStatus::cases())->mapWithKeys(
                    fn (QualityPeriodStatus $status) => [$status->value => $status->getLabel()],
                )->all())
                ->default(QualityPeriodStatus::Draft->value)
                ->required()
                ->native(false),
            Toggle::make($prefix.'is_active')
                ->label('Aktif')
                ->default(true),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::fields());
    }
}
