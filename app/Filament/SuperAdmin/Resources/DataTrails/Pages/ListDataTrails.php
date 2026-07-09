<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Pages;

use App\Filament\SuperAdmin\Resources\DataTrails\DataTrailsResource;
use App\Filament\SuperAdmin\Resources\DataTrails\Schemas\DataTrailsScopeForm;
use App\Support\TraceabilityLinks\TraceabilityLinkScopeResolver;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListDataTrails extends ListRecords
{
    #[Url]
    public ?string $selfAssessmentId = null;

    protected static string $resource = DataTrailsResource::class;

    public function updatedSelfAssessmentId(): void
    {
        $this->resetTable();
    }

    public function hasCompleteScope(): bool
    {
        return DataTrailsScopeForm::isComplete($this->scopeFilters());
    }

    /**
     * @return array<string, mixed>
     */
    protected function scopeFilters(): array
    {
        return DataTrailsScopeForm::filterPayload($this->selfAssessmentId);
    }

    protected function getTableQuery(): Builder
    {
        return TraceabilityLinkScopeResolver::apply(
            parent::getTableQuery(),
            $this->scopeFilters(),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Penilaian Mandiri')
                    ->description('Pilih penilaian mandiri untuk melihat jejak ketertelusuran dari unit dan periode terkait.')
                    ->schema(DataTrailsScopeForm::schema())
                    ->columnSpanFull(),
                Placeholder::make('timeline_hint')
                    ->hiddenLabel()
                    ->content('Pilih penilaian mandiri untuk menampilkan timeline.')
                    ->visible(fn (): bool => ! $this->hasCompleteScope()),
                EmbeddedTable::make()
                    ->visible(fn (): bool => $this->hasCompleteScope()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
