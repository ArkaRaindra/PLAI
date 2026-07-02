<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Schemas;

use App\Enums\QualityPeriodStatus;
use App\Filament\SuperAdmin\Resources\QualityPeriods\Schemas\QualityPeriodForm;
use App\Models\QualityPeriod;
use App\Models\Standard;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Kalnoy\Nestedset\NestedSet;
use Wsmallnews\FilamentNestedset\Forms\Fields\KalnoyNestedsetSelectTree;

class StandardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('standard_source_id')
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Hidden::make('include_standard_version')
                    ->default(true)
                    ->dehydrated(fn (string $operation, Get $get, ?Standard $record): bool => self::isRootStandard($operation, $get, $record)),
                Radio::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'AKTIF',
                        0 => 'TIDAK AKTIF',
                    ])
                    ->default(true)
                    ->columnSpanFull(),
                KalnoyNestedsetSelectTree::make('parent_id')
                    ->label('Induk Standar')
                    ->searchable()
                    ->query(
                        fn (Get $get) => Standard::scoped([
                            'standard_source_id' => (int) $get('standard_source_id'),
                        ])->defaultOrder(),
                        titleAttribute: 'name',
                        parentAttribute: NestedSet::PARENT_ID,
                    )
                    ->enableBranchNode()
                    ->withCount()
                    ->placeholder('Pilih induk standar')
                    ->emptyLabel('Tidak ada induk standar')
                    ->treeKey('StandardParentId')
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                        if (blank($state)) {
                            $set('_inherited_quality_period', null);
                            $set('_inherited_version', null);

                            return;
                        }

                        $parent = Standard::query()
                            ->with('standardVersion.qualityPeriod')
                            ->find($state);

                        $set('_inherited_quality_period', $parent?->standardVersion?->qualityPeriod?->name);
                        $set('_inherited_version', $parent?->standardVersion?->version);
                    })
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->extraInputAttributes([
                        'style' => 'text-transform: uppercase',
                    ])
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state !== null ? strtoupper($state) : null),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->fileAttachments(false)
                    ->nullable()
                    ->extraAttributes([
                        'style' => 'min-height: 300px;',
                    ])
                    ->columnSpanFull(),
                Section::make('Periode Kualitas & Versi Standar')
                    ->description('Mengikuti induk standar — tidak dapat diubah pada sub-standar.')
                    ->visible(fn (string $operation, Get $get, ?Standard $record): bool => self::isChildStandard($operation, $get, $record))
                    ->schema([
                        TextInput::make('_inherited_quality_period')
                            ->label('Periode Kualitas')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('_inherited_version')
                            ->label('Versi')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Periode Kualitas')
                    ->visible(fn (string $operation, Get $get, ?Standard $record): bool => self::isRootStandard($operation, $get, $record))
                    ->schema([
                        Radio::make('quality_period_mode')
                            ->label('Sumber Periode')
                            ->options([
                                'existing' => 'Pilih periode existing',
                                'new' => 'Buat periode baru',
                            ])
                            ->default('existing')
                            ->live()
                            ->columnSpanFull(),
                        Select::make('quality_period_id')
                            ->label('Periode Kualitas')
                            ->options(fn (): array => QualityPeriod::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('quality_period_mode') === 'existing')
                            ->required(fn (Get $get): bool => $get('quality_period_mode') === 'existing')
                            ->columnSpanFull(),
                        ...collect(QualityPeriodForm::fields('qualityPeriod.'))
                            ->map(function ($field) {
                                $field = $field->visible(fn (Get $get): bool => $get('quality_period_mode') === 'new');

                                if ($field->getName() !== 'qualityPeriod.is_active') {
                                    $field->required(fn (Get $get): bool => $get('quality_period_mode') === 'new');
                                }

                                return $field;
                            })
                            ->all(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Versi Standar')
                    ->visible(fn (string $operation, Get $get, ?Standard $record): bool => self::isRootStandard($operation, $get, $record))
                    ->schema([
                        Radio::make('standardVersion.is_active')
                            ->label('Status Data Versi Standar')
                            ->options([
                                true => 'AKTIF',
                                false => 'TIDAK AKTIF',
                            ])
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('standardVersion.version')
                            ->label('Versi')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('standardVersion.start_date')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->native(false)->required(),
                        DatePicker::make('standardVersion.end_date')
                            ->label('Tanggal Selesai')
                            ->nullable()
                            ->native(false)->nullable(),
                        Select::make('standardVersion.status')
                            ->label('Status')
                            ->options(collect(QualityPeriodStatus::cases())->mapWithKeys(
                                fn (QualityPeriodStatus $status) => [$status->value => $status->getLabel()],
                            )->all())
                            ->default(QualityPeriodStatus::Draft->value)
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function isChildStandard(string $operation, Get $get, ?Standard $record): bool
    {
        if ($operation === 'create') {
            return filled($get('parent_id'));
        }

        return filled($record?->parent_id);
    }

    protected static function isRootStandard(string $operation, Get $get, ?Standard $record): bool
    {
        if ($operation === 'create') {
            return blank($get('parent_id'));
        }

        return $record?->parent_id === null;
    }
}
