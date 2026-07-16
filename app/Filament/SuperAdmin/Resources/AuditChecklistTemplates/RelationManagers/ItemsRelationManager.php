<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\RelationManagers;

use App\Models\StandardVersion;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Checklist';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('standard_version_id')
                    ->label('Versi Standar')
                    ->relationship('standardVersion', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn (StandardVersion $record): string => "{$record->standard?->name} — {$record->version}",
                    )
                    ->getSearchResultsUsing(function (string $search): array {
                        return StandardVersion::query()
                            ->with('standard')
                            ->whereHas('standard', fn ($q) => $q->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%"))
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('sequence')
                    ->label('No.')
                    ->sortable(),

                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->limit(80),

                TextColumn::make('standardVersion.standard.name')
                    ->label('Standar'),

                TextColumn::make('standardVersion.version')
                    ->label('Versi Standar'),
            ])
            ->defaultSort('sequence')
            ->reorderable('sequence')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
