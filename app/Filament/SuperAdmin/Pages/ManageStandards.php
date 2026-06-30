<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Standard;
use App\Models\StandardSource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Kalnoy\Nestedset\NestedSet;
use LaraZeus\Tabler\Tabler;
use Livewire\Attributes\Url;
use Wsmallnews\FilamentNestedset\Forms\Fields\KalnoyNestedsetSelectTree;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;

class ManageStandards extends NestedsetPage
{
    #[Url]
    public ?string $standardSourceId = null;

    protected static ?string $model = Standard::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::ListTree;

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Standar';

    protected static ?string $modelLabel = 'Standar';

    protected static ?string $pluralModelLabel = 'Standar';

    protected static string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static ?string $emptyLabel = 'Belum ada standar';

    protected static ?string $emptyTipLabel = 'Buat standar pertama untuk sumber ini.';

    protected string $view = 'filament.super-admin.pages.manage-standards';

    public function hasSelectedSource(): bool
    {
        return filled($this->standardSourceId);
    }

    public function updatedStandardSourceId(): void
    {
        $this->cachedHeaderActions = [];
        $this->cacheInteractsWithHeaderActions();
    }

    public function createAction(): Action
    {
        return parent::createAction()
            ->label('Buat Standar')
            ->icon(Heroicon::Plus);
    }

    public function createChildAction(): Action
    {
        return parent::createChildAction()
            ->label('Tambah Sub-standar');
    }

    protected function getParentSelect(): array|Field
    {
        return KalnoyNestedsetSelectTree::make('parent_id')
            ->label('Induk Standar')
            ->level(is_null($this->getLevel()) ? null : ($this->getLevel() - 1))
            ->searchable()
            ->query(function () {
                return $this->getQuery();
            }, titleAttribute: 'name', parentAttribute: NestedSet::PARENT_ID)
            ->enableBranchNode()
            ->withCount()
            ->placeholder('Pilih induk standar')
            ->emptyLabel('Tidak ada induk standar')
            ->treeKey('NestedParentId');
    }

    public function fixTreeAction(): Action
    {
        return parent::fixTreeAction()
            ->label('Perbaiki Tree');
    }

    /**
     * @return array<string, int>
     */
    public function nestedScoped(): array
    {
        if (! $this->hasSelectedSource()) {
            return [];
        }

        return ['standard_source_id' => (int) $this->standardSourceId];
    }

    public function getEloquentQuery($query)
    {
        if (! $this->hasSelectedSource()) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        if (! $this->hasSelectedSource()) {
            return [];
        }

        return parent::getHeaderActions();
    }

    protected function getViewData(): array
    {
        if (! $this->hasSelectedSource()) {
            return ['nestedset' => collect()];
        }

        return parent::getViewData();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('standardSourceId')
                            ->label('Sumber Standar')
                            ->placeholder('— Pilih Sumber Standar —')
                            ->options(fn (): array => StandardSource::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->live(debounce: 300)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function schema(array $arguments): array
    {
        return [
            Radio::make('is_active')
                ->label('Status')
                ->options([
                    1 => 'AKTIF',
                    0 => 'TIDAK AKTIF',
                ])
                ->default(true)
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
            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ];
    }

    protected function infolistSchema(): array
    {
        return [];
    }

    public function getRecordLabel(Model $record): HtmlString|string
    {
        $code = e($record->code);
        $name = e($record->name);
        $isActive = (bool) $record->is_active;
        $statusLabel = $isActive ? 'AKTIF' : 'TIDAK AKTIF';
        $statusClasses = $isActive
            ? 'bg-success-50 text-success-700 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/20'
            : 'bg-danger-50 text-danger-700 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/20';

        $descriptionHtml = filled($record->description)
            ? '<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">'.e($record->description).'</p>'
            : '';

        return new HtmlString(<<<HTML
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="font-semibold text-gray-950 dark:text-white">{$code}</span>
                    <span class="text-gray-400">—</span>
                    <span class="text-gray-950 dark:text-white">{$name}</span>
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {$statusClasses}">{$statusLabel}</span>
                </div>
                {$descriptionHtml}
            </div>
            HTML);
    }
}
