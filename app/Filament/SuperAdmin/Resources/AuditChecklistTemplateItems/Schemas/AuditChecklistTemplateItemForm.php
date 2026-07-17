<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Schemas;

use App\Models\StandardVersion;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditChecklistTemplateItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('template_id'),
                Textarea::make('question')
                    ->label('Pertanyaan Audit')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('standard_version_id')
                    ->label('Mapping Standar')
                    ->helperText('Opsional — hubungkan pertanyaan ini ke versi standar mutu yang relevan.')
                    ->searchable()
                    ->preload()
                    ->getSearchResultsUsing(function (string $search): array {
                        return StandardVersion::query()
                            ->with('standard')
                            ->whereHas('standard', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (StandardVersion $version): array => [
                                $version->id => "{$version->standard?->name} (v{$version->version})",
                            ])
                            ->all();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $version = StandardVersion::query()->with('standard')->find($value);

                        return $version === null ? null : "{$version->standard?->name} (v{$version->version})";
                    }),
            ]);
    }
}