<?php

namespace App\Filament\SuperAdmin\Resources\Targets\Schemas;

use App\Models\Indicator;
use App\Models\QualityPeriod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class TargetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('indicator_id')
                    ->label('Indikator')
                    ->options(Indicator::query()->orderBy('code')->pluck('code', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih indikator')
                    ->live()
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, callable $get): Unique => $rule
                            ->where('quality_period_id', $get('quality_period_id')),
                        ignoreRecord: true,
                    )
                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                        if (blank($state)) {
                            $set('quality_period_id', null);

                            return;
                        }

                        $indicator = Indicator::query()
                            ->with([
                                'standardVersion:id,quality_period_id',
                            ])
                            ->find($state);

                        $set(
                            'quality_period_id',
                            $indicator?->standardVersion?->quality_period_id,
                        );
                    })
                    ->native(false),
                Select::make('quality_period_id')
                    ->label('Periode Kualitas')
                    ->options(fn (Get $get): array => QualityPeriod::query()
                        ->whereKey($get('quality_period_id'))
                        ->pluck('code', 'id')
                        ->all())
                    ->placeholder(fn (Get $get): string => blank($get('indicator_id'))
                        ? 'Pilih indikator terlebih dahulu'
                        : 'Tidak ada')
                    ->disabled()
                    ->dehydrated()
                    ->native(false),
                TextInput::make('target_value')
                    ->label('Target')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(999999)
                    ->step(1)
                    ->placeholder('Masukkan target'),
            ]);
    }
}
