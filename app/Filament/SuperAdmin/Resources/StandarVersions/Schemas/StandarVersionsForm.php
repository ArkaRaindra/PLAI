<?php

namespace App\Filament\SuperAdmin\Resources\StandarVersions\Schemas;

use App\Models\QualityPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StandarVersionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('is_active')
                    ->label('Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->default('1')
                    ->columnSpanFull(),
                Select::make('quality_period_id')
                    ->label('Kode Periode')
                    ->options(fn (): array => QualityPeriod::query()
                    ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()),
                TextInput::make('version')
                    ->label('Versi')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now())
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->default(now())
                    ->native(false),
                Select::make('status')
                ->label('Status')
                ->options([
                    'draft' => 'Draft',
                    'active' => 'Aktif',
                    'closed' => 'Tutup',
                ])
            ]);
    }
}
