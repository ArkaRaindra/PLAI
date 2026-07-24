<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages;

use App\Filament\SuperAdmin\Resources\AuditFindings\AuditFindingResource;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\CorrectiveActionResource;
use App\Models\CorrectiveAction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListCorrectiveActions extends ListRecords
{
    public ?string $auditFIndingId = null;

    protected static string $resource = CorrectiveActionResource::class;

    protected static ?string $title = 'Corrective Action';

    protected static ?string $breadcrumb = 'Corrective Action';

    public function mount(): void
    {
        if (blank($this->auditFIndingId)) {
            $this->redirect(AuditFindingResource::getUrl('index'));

            return;
        }

        $existing = CorrectiveAction::query()
            ->where('audit_finding_id', $this->auditFIndingId)
            ->first();

        if ($existing !== null) {
            $this->redirect(CorrectiveActionResource::getUrl('view', ['record' => $existing->id]));

            return;
        }

        $this->redirect(CorrectiveActionResource::getCreateUrl($this->auditFIndingId));
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Corrective Action',
        ];
    }

    public function getTableQuery(): Builder
    {
        if (blank($this->auditFIndingId)) {
            return CorrectiveAction::query()->whereRaw('1 = 0');
        }

        return CorrectiveAction::query()
            ->where('audit_finding_id', (int) $this->auditFIndingId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->auditFIndingId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditFindingResource::getUrl('view', ['record' => $this->auditFindingId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Buat Corrective Action')
                ->icon(Heroicon::Plus)
                ->url(CorrectiveActionResource::getCreateUrl($this->auditFIndingId)),
        ];
    }
}
