<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorMappings\Schemas;

use App\Models\Indicator;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IndicatorMappingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('is_primary')
                    ->label('Data Utama')
                    ->options([
                        true => 'Ya',
                        false => 'Bukan ',
                    ])
                    ->default(true)
                    ->columnSpanFull(),
                Select::make('internal_indicator_id')
                    ->label('Indikator Internal')
                    ->options(Indicator::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->preload(),
                    // ->requiredWithout('external_indicator_id')
                    // ->validationMessages([
                    //     'required_without' => 'Indikator Internal atau Indikator Eksternal wajib diisi.',
                    // ]),
                Select::make('external_indicator_id')
                    ->label('Indikator Eksternal')
                    ->options(Indicator::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                    // ->requiredWithout('internal_indicator_id')
                    // ->validationMessages([
                    //     'required_without' => 'Indikator Internal atau Indikator Eksternal wajib diisi.',
                    // ]),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
