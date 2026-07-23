<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use App\Models\AuditChecklistTemplate;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;

class CreateAuditChecklistTemplateItem extends CreateRecord
{
    #[Url]
    public ?string $templateId = null;

    protected static string $resource = AuditChecklistTemplateItemResource::class;

    protected static ?string $title = 'Tambah Item Checklist';

    protected static bool $canCreateAnother = true;

    public function mount(): void
    {
        if (blank($this->templateId)) {
            $this->redirect(AuditChecklistTemplateResource::getUrl('index'));

            return;
        }

        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        if (blank($this->templateId)) {
            return parent::getBreadcrumbs();
        }

        $template = AuditChecklistTemplate::query()->find($this->templateId);

        if ($template === null) {
            return parent::getBreadcrumbs();
        }

        return [
            AuditChecklistTemplateResource::getUrl('index') => AuditChecklistTemplateResource::getNavigationLabel(),
            AuditChecklistTemplateItemResource::getListUrl($template->id) => $template->name,
            'Tambah Item Checklist',
        ];
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->templateId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditChecklistTemplateItemResource::getListUrl($this->templateId))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            'template_id' => (int) $this->templateId,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['template_id'] = (int) $this->templateId;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AuditChecklistTemplateItemResource::getListUrl($this->templateId);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->url(AuditChecklistTemplateItemResource::getListUrl($this->templateId));
    }
}