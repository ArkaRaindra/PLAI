<?php

namespace App\Filament\SuperAdmin\Resources\AuditFindings\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Models\AuditAssignment;
use App\Models\AuditFinding;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListAuditFindings extends ListRecords
{
    #[Url]
    public ?string $auditAssignmentId = null;

    protected static string $resource = AuditFindingResource::class;

    protected static ?string $title = 'Audit Findings';

    protected static ?string $breadcrumb = 'Audit Findings';

    public function mount(): void
    {
        if (blank($this->auditAssignmentId)) {
            $this->redirect(AuditAssignmentResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (blank($this->auditAssignmentId)) {
            return parent::getBreadcrumbs();
        }

        $assignment = AuditAssignment::query()->find($this->auditAssignmentId);

        if ($assignment === null) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditAssignmentResource::getListUrl($assignment->audit_cycle_id) => 'Audit Assignment',
            AuditFindingResource::getListUrl($assignment->id) => 'Audit Findings',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->auditAssignmentId)) {
            return AuditFinding::query()->whereRaw('1 = 0');
        }

        return AuditFinding::query()
            ->where('audit_assignment_id', (int) $this->auditAssignmentId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditAssignmentId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditAssignmentResource::getUrl('view', ['record' => $this->auditAssignmentId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tambah Temuan')
                ->icon(Heroicon::Plus)
                ->url(AuditFindingResource::getCreateUrl($this->auditAssignmentId)),
        ];
    }
}