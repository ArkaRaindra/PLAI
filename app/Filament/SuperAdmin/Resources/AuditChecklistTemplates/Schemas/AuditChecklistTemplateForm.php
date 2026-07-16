<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas;

use App\Models\StandardVersion;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditChecklistTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Template')
                    ->required()
                    ->maxLength('255')
                    ->columnSpanFull(),
                Hidden::make('version_no')
                    ->default(1)
                    ->dehydrated(fn (string $operation): bool => $operation === 'create'),
                Repeater::make('items')
                    ->label('Item Checklist')
                    ->relationship('items')
                    ->orderColumn('sequence')
                    ->schema([
                        Select::make('standard_version_id')
                            ->label('Versi Standar')
                            ->relationship('standardVersion', 'id')
                            ->getOptionLabelFromRecordUsing(
                                fn (StandardVersion $record): string => "{$record->standard?->name} — {$record->version}" 
                            )
                            ->getSearchResultsUsing(function (string $search): array {
                                return StandardVersion::query()
                                    ->wirh('standard')
                                    ->whereHas('standard', fn ($q) => $q->where('name', 'like', "%{$search}%")
                                        ->orWhere('code', 'like', "%{search}%"))
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (StandardVersion $record): array => [
                                        $record->id => "{$record->standard?->name} — {$record->version}",
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        Textarea::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Item Baru')
                    ->addActionLabel('Tambah Item')
                    ->columnSpanFull(),
            ]);
    }
}
