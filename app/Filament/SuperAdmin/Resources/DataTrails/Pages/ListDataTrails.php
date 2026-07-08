<?php

namespace App\Filament\SuperAdmin\Resources\DataTrails\Pages;

use App\Filament\SuperAdmin\Resources\DataTrails\DataTrailsResource;
use App\Filament\SuperAdmin\Resources\DataTrails\Schemas\DataTrailsScopeForm;
use App\Models\StandardVersion;
use App\Support\TraceabilityLinks\TraceabilityLinkScopeResolver;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListDataTrails extends ListRecords
{
    #[Url]
    public ?string $standardSourceId = null;

    #[Url]
    public ?string $qualityPeriodId = null;

    #[Url]
    public ?string $standardVersionId = null;

    #[Url]
    public ?string $standardId = null;

    protected static string $resource = DataTrailsResource::class;

    public function updatedStandardSourceId(): void
    {
        $this->qualityPeriodId = null;
        $this->standardVersionId = null;
        $this->standardId = null;
        $this->resetTable();
    }

    public function updatedQualityPeriodId(): void
    {
        if (blank($this->standardVersionId)) {
            $this->standardId = null;
            $this->resetTable();

            return;
        }

        $exists = StandardVersion::query()
            ->whereKey((int) $this->standardVersionId)
            ->when(
                filled($this->qualityPeriodId),
                fn (Builder $query): Builder => $query->where('quality_period_id', (int) $this->qualityPeriodId),
            )
            ->exists();

        if (! $exists) {
            $this->standardVersionId = null;
        }

        $this->standardId = null;
        $this->resetTable();
    }

    public function updatedStandardVersionId(): void
    {
        $this->standardId = null;
        $this->resetTable();
    }

    public function updatedStandardId(): void
    {
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        return TraceabilityLinkScopeResolver::apply(
            parent::getTableQuery(),
            DataTrailsScopeForm::filterPayload(
                $this->standardSourceId,
                $this->qualityPeriodId,
                $this->standardVersionId,
                $this->standardId,
            ),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lingkup Data')
                    ->description('Pilih lingkup standar untuk menampilkan jejak ketertelusuran yang relevan.')
                    ->schema(DataTrailsScopeForm::schema())
                    ->columns(4)
                    ->columnSpanFull(),
                EmbeddedTable::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
