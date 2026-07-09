<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Schemas;

use App\Models\IndicatorOwner;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\Target;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class RealizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('target_id'),
                Select::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship(
                        name: 'organizationUnit',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, Get $get): Builder {
                            $targetId = $get('target_id');

                            if (blank($targetId)) {
                                return $query->whereRaw('1 = 0');
                            }

                            $indicatorOwnerUnitIds = IndicatorOwner::query()
                                ->where('indicator_id', Target::query()
                                    ->whereKey($targetId)
                                    ->value('indicator_id'))
                                ->pluck('organization_unit_id');

                            if ($indicatorOwnerUnitIds->isEmpty()) {
                                return $query->whereRaw('1 = 0');
                            }

                            $usedUnitIds = Realization::query()
                                ->where('target_id', $targetId)
                                ->when(
                                    filled($get('id')),
                                    fn (Builder $query) => $query->where('id', '!=', $get('id')),
                                )
                                ->pluck('organization_unit_id');

                            return $query
                                ->where('is_active', true)
                                ->whereIn('id', $indicatorOwnerUnitIds)
                                ->when(
                                    $usedUnitIds->isNotEmpty(),
                                    fn (Builder $query) => $query->whereNotIn('id', $usedUnitIds),
                                )
                                ->orderBy('name');
                        },
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (OrganizationUnit $record): string => "{$record->code} — {$record->name}",
                    )
                    ->searchable(['code', 'name'])
                    ->preload()
                    ->required()
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, callable $get): Unique => $rule
                            ->where('target_id', $get('target_id')),
                        ignoreRecord: true,
                    ),
                TextInput::make('actual_value')
                    ->label('Nilai Aktual')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01),
                TextInput::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01),
                Hidden::make('status')
                    ->default('draft')
                    ->dehydrated(),
                Textarea::make('note_rejected')
                    ->label('Catatan Penolakan')
                    ->visible(fn (Get $get): bool => $get('status') === 'rejected')
                    ->required(fn (Get $get): bool => $get('status') === 'rejected')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
