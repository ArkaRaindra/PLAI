<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Schemas;

use App\Enums\QualityPeriodStatus;
use App\Filament\SuperAdmin\Resources\QualityPeriodes\Schemas\QualityPeriodeForm;
use App\Models\QualityPeriode;
use App\Models\Standard;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->columnSpanFull(),
                Toggle::make('include_standard_version')
                    ->label('Tautkan Versi Standar')
                    ->helperText('Opsional — kosongkan jika standar belum perlu versi.')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),
                Section::make('Periode Kualitas')
                    ->visible(fn (Get $get): bool => (bool) $get('include_standard_version'))
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
                            ->options(fn (): array => QualityPeriode::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->visible(fn (Get $get): bool => $get('quality_period_mode') === 'existing')
                            ->required(fn (Get $get): bool => (bool) $get('include_standard_version') && $get('quality_period_mode') === 'existing')
                            ->columnSpanFull(),
                        ...collect(QualityPeriodeForm::fields('qualityPeriode.'))
                            ->map(function ($field) {
                                $field = $field->visible(fn (Get $get): bool => $get('quality_period_mode') === 'new');

                                if ($field->getName() !== 'qualityPeriode.is_active') {
                                    $field->required(fn (Get $get): bool => (bool) $get('include_standard_version') && $get('quality_period_mode') === 'new');
                                }

                                return $field;
                            })
                            ->all(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Versi Standar')
                    ->visible(fn (Get $get): bool => (bool) $get('include_standard_version'))
                    ->schema([
                        TextInput::make('standardVersion.version')
                            ->label('Versi')
                            ->required(fn (Get $get): bool => (bool) $get('include_standard_version'))
                            ->maxLength(255),
                        DatePicker::make('standardVersion.start_date')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->native(false),
                        DatePicker::make('standardVersion.end_date')
                            ->label('Tanggal Selesai')
                            ->nullable()
                            ->native(false),
                        Select::make('standardVersion.status')
                            ->label('Status')
                            ->options(collect(QualityPeriodStatus::cases())->mapWithKeys(
                                fn (QualityPeriodStatus $status) => [$status->value => $status->getLabel()],
                            )->all())
                            ->default(QualityPeriodStatus::Draft->value)
                            ->native(false),
                        Toggle::make('standardVersion.is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
