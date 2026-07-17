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

class ListAuditChecklistResponses extends ListRecords
{
    public ?string $auditAsssignmentId = null;

    protected static string $resource = AuditChecklistResponseResource::class;

    protected static ?string $title = 'Audit Response';

    protected static ?string $breadcrumb = 'Audit Response';

    public function mount(): void
    {
        if (blank($this->auditAsssignmentId)) {
            $this->redirect(AuditAssignmentResource::getUrl('index'));

            return;
        }
        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        if (blank($this->auditAsssignmentId)) {
            return parent::getBreadcrumbs();
        }

        $assignment = AuditAssignment::query()->find($this->auditAsssignmentId);

        if ($assignment === null) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditAssignmentResource::getlistUrl($assignment->audit_cycle_id) => 'Audit Assignment',
            AuditChecklistResponseResource::getListurl($assignment->id) => 'Audit Response',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->auditAsssignmentId)) {
            return AuditChecklistResponse::query()->whereRaw('1 = 0');
        }

        return AuditChecklistResponse::query()
            ->where('audit_assignment_id', (int) $this->auditAsssignmentId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditAsssignmentId)) {
            return [];
        }
    
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditAssignmentResource::getUrl('view', ['record' => $this->auditAsssignmentId]))
                ->button()
                ->colot('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Isi Jawaban')
                ->icon(Heroicon::Plus)
                ->url(AuditChecklistResponseResource::getCreateUrl($this->auditAsssignmentId)),
        ];
    }
}
