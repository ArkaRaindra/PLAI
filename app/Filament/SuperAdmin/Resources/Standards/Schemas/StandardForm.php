<?php

namespace App\Filament\SuperAdmin\Resources\Standards\Schemas;

use App\Models\Standard;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
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
            ]);
    }
}
