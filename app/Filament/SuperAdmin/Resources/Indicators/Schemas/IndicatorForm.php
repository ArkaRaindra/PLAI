<?php

namespace App\Filament\SuperAdmin\Resources\Indicators\Schemas;

use App\Models\Indicator;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class IndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('standard_id'),
                Select::make('parent_indicator_id')
                    ->label('Induk Indikator')
                    ->relationship(
                        name: 'parentIndicator',
                        titleAttribute: 'name',
                        ignoreRecord: true,
                        modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query
                            ->where('standard_id', $get('standard_id'))
                            ->orderBy('code'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Indicator $record): string => "{$record->code} — {$record->name}",
                    )
                    ->searchable(['code', 'name'])
                    ->preload()
                    ->nullable()
                    ->placeholder('Tidak ada induk'),
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes([
                        'style' => 'text-transform: uppercase',
                    ])
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state !== null ? strtoupper($state) : null)
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, callable $get) => $rule->where('standard_id', $get('standard_id')),
                        ignoreRecord: true,
                    ),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('calculation_method')
                    ->label('Metode Perhitungan')
                    ->placeholder('Pilih metode perhitungan')
                    ->options(Indicator::options())
                    ->native(false)
                    ->searchable()
                    ->nullable(),
                TextInput::make('measurement_unit')
                    ->label('Satuan Pengukuran')
                    ->maxLength(255),
                TextInput::make('weight')
                    ->label('Bobot')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->nullable(),
            ]);
    }
}
