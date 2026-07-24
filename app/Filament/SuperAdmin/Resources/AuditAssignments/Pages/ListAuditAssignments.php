<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditCycles\AuditCycleResource;
use App\Models\AuditAssignment;
use App\Models\AuditCycle;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListAuditAssignments extends ListRecords
{
    #[Url]
    public ?string $auditCycleId = null;

    protected static string $resource = AuditAssignmentResource::class;

    protected static ?string $title = 'Audit Assignment';

    protected static ?string $breadcrumb = 'Audit Assignment';

    public function mount(): void
    {
        if (blank($this->auditCycleId)) {
            $this->redirect(AuditCycleResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        if (blank($this->auditCycleId)) {
            return parent::getBreadcrumbs();
        }

        $cycle = AuditCycle::query()->find($this->auditCycleId);

        if ($cycle === null) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditCycleResource::getUrl('index') => AuditCycleResource::getNavigationLabel(),
            AuditCycleResource::getUrl('view', ['record' => $cycle->id]) => $cycle->displayTitle(),
            AuditAssignmentResource::getListUrl($cycle->id) => 'Audit Assignment',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->auditCycleId)) {
            return AuditAssignment::query()->whereRaw('1 = 0');
        }

        return AuditAssignment::query()
            ->where('audit_cycle_id', (int) $this->auditCycleId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditCycleId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditCycleResource::getUrl('view', ['record' => $this->auditCycleId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tugaskan Auditor')
                ->icon(Heroicon::Plus)
                ->url(AuditAssignmentResource::getCreateUrl($this->auditCycleId)),
        ];
    }
}
