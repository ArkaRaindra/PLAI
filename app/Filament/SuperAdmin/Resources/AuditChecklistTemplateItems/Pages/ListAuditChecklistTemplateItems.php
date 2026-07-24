<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\Pages;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;
use App\Models\AuditChecklistTemplate;
use App\Models\AuditChecklistTemplateItem;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListAuditChecklistTemplateItems extends ListRecords
{
    #[Url]
    public ?string $templateId = null;

    protected static string $resource = AuditChecklistTemplateItemResource::class;

    protected static ?string $title = 'Item Checklist';

    protected static ?string $breadcrumb = 'Item Checklist';

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
            AuditChecklistTemplateResource::getUrl('view', ['record' => $template->id]) => $template->name,
            AuditChecklistTemplateItemResource::getListUrl($template->id) => 'Item Checklist',
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (blank($this->templateId)) {
            return AuditChecklistTemplateItem::query()->whereRaw('1 = 0');
        }

        return AuditChecklistTemplateItem::query()
            ->where('template_id', (int) $this->templateId);
    }

    protected function getHeaderActions(): array
    {
        if (blank($this->templateId)) {
            return [];
        }

        return [
            Action::make('back')
                ->label('Kembali')
                ->url(AuditChecklistTemplateResource::getUrl('view', ['record' => $this->templateId]))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            CreateAction::make()
                ->label('Tambah Item')
                ->icon(Heroicon::Plus)
                ->url(AuditChecklistTemplateItemResource::getCreateUrl($this->templateId)),
        ];
    }
}
