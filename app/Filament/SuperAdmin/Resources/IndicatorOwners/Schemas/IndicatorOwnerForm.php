<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Schemas;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\UserPosition;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class IndicatorOwnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('is_primary')
                    ->label('Data Utama')
                    ->options([
                        true => 'Ya',
                        false => 'Bukan',
                    ])
                    ->default(true)
                    ->columnSpanFull(),
                Select::make('indicator_id')
                    ->label('Indikator')
                    ->options(Indicator::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->options(OrganizationUnit::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                // Select::make('user_position_id')
                //     ->label('Jabatan')
                //     ->options(UserPosition::query()->pluck('name', 'id'))
                //     ->searchable()
                //     ->preload()
                //     ->required(),
                RichEditor::make('notes')
                    ->label('Catatan')
                    ->extraAttributes([
                        'style' => 'min-height: 300px;',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
