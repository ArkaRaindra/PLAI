<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Pages;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistResponses\AuditChecklistResponseResource;
use App\Models\AuditAssignment;
use App\Models\AuditChecklistResponse;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListAuditChecklistResponses extends ListRecords
{
    #[Url]
    public ?string $auditAssignmentId = null;

    protected static string $resource = AuditChecklistResponseResource::class;

    protected static ?string $title = 'Audit Response';

    protected static ?string $breadcrumb = 'Audit Response';

    public function mount(): void
    {
        if (blank($this->auditAssignmentId)) {
            $this->redirect(AuditAssignmentResource::getUrl('index'));

            return;
        }
        parent::mount();
    }

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
            AuditChecklistResponseResource::getListUrl($assignment->id) => 'Audit Response',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->auditAssignmentId)) {
            return AuditChecklistResponse::query()->whereRaw('1 = 0');
        }

        return AuditChecklistResponse::query()
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
                ->label('Isi Jawaban')
                ->icon(Heroicon::Plus)
                ->url(AuditChecklistResponseResource::getCreateUrl($this->auditAssignmentId)),
        ];
    }
}
