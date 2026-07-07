<?php

namespace App\Filament\SuperAdmin\Resources\IndicatorOwners\Schemas;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\UserPosition;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('user_position_id', null))
                    ->required(),
                Select::make('user_position_id')
                    ->label('Pengguna Yang Menjabat di Unit Organisasi')
                    ->relationship(
                        name: 'userPosition',
                        titleAttribute: 'id',
                        modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query
                            ->with(['user', 'position', 'organizationUnit'])
                            ->where('is_active', true)
                            ->when(
                                filled($get('organization_unit_id')),
                                fn (Builder $query) => $query->where('organization_unit_id', $get('organization_unit_id')),
                                fn (Builder $query) => $query->whereRaw('1 = 0'),
                            ),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (UserPosition $record): string => "{$record->user->name} — {$record->position->name} — {$record->organizationUnit->name}",
                    )
                    ->disabled(fn (Get $get): bool => blank($get('organization_unit_id')))
                    ->placeholder(fn (Get $get): string => blank($get('organization_unit_id'))
                        ? 'Pilih unit organisasi terlebih dahulu'
                        : 'Tidak ada')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
