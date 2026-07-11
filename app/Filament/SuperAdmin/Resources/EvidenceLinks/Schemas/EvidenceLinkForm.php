<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceLinks\Schemas;

use App\Support\EvidenceLink\LinkableTypeRegistry;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EvidenceLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('evidence_id'),
                Select::make('reference_type')
                    ->label('Jenis Referensi')
                    ->options(LinkableTypeRegistry::options())
                    ->native(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('reference_id', null))
                    ->disableOptionWhen(fn (string $value): bool => ! LinkableTypeRegistry::isAvailable($value)),
                Select::make('reference_id')
                    ->label('Pilih Item')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (Get $get): bool => blank($get('reference_type'))
                        || ! LinkableTypeRegistry::isAvailable($get('reference_type')))
                    ->helperText(fn (Get $get): ?string => (blank($get('reference_type'))
                        || LinkableTypeRegistry::isAvailable($get('reference_type')))
                        ? null
                        : 'Modul untuk jenis referensi ini belum tersedia.')
                    ->options(function (Get $get): array {
                        $type = $get('reference_type');

                        if (blank($type) || ! LinkableTypeRegistry::isAvailable($type)) {
                            return [];
                        }

                        $model = LinkableTypeRegistry::modelFor($type);
                        $titleAttribute = LinkableTypeRegistry::titleAttributeFor($type);

                        return $model::query()
                            ->orderBy($titleAttribute)
                            ->limit(50)
                            ->pluck($titleAttribute, 'id')
                            ->all();
                    })
                    ->getSearchResultsUsing(function (Get $get, string $search): array {
                        $type = $get('reference_type');

                        if (blank($type) || ! LinkableTypeRegistry::isAvailable($type)) {
                            return [];
                        }

                        $model = LinkableTypeRegistry::modelFor($type);
                        $titleAttribute = LinkableTypeRegistry::titleAttributeFor($type);

                        return $model::query()
                            ->where($titleAttribute, 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck($titleAttribute, 'id')
                            ->all();
                    }),
            ]);
    }
}