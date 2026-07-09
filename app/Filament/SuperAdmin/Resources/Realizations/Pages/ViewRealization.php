<?php

namespace App\Filament\SuperAdmin\Resources\Realizations\Pages;

use App\Events\EvidenceRecorded;
use App\Filament\SuperAdmin\Resources\Realizations\RealizationResource;
use App\Filament\SuperAdmin\Resources\Targets\TargetResource;
use App\Models\EvidenceVersions;
use App\Models\Realization;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ViewRealization extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = RealizationResource::class;

    protected string $view = 'filament.super-admin.resources.realizations.pages.view-realization';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing([
            'evidenceLink.evidence',
            'target.indicator',
            'target.qualityPeriod',
            'organizationUnit',
            'statusHistories.user',
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getInfolistContentComponent(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => $this->record->evidenceVersionsQuery())
            ->heading('Bukti')
            ->description('Riwayat versi bukti pendukung realisasi.')
            ->columns([
                TextColumn::make('version')
                    ->label('Versi')
                    ->sortable(),
                TextColumn::make('evidence.title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'file' => 'File',
                        'url' => 'URL',
                        default => $state,
                    }),
                TextColumn::make('uploadedBy.name')
                    ->label('Diupload Oleh')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('addEvidence')
                    ->label('Tambah Bukti')
                    ->icon(Heroicon::Plus)
                    ->visible(fn(): bool => $this->record->canManageEvidence())
                    ->schema(self::evidenceFormSchema(requireFile: true))
                    ->action(function (array $data): void {
                        $this->storeEvidence($data);

                        Notification::make()
                            ->title('Bukti berhasil disimpan')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('previewEvidence')
                    ->label('Pratinjau')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->url(fn(EvidenceVersions $record): ?string => self::evidencePreviewUrl($record))
                    ->openUrlInNewTab()
                    ->visible(fn(EvidenceVersions $record): bool => self::evidencePreviewUrl($record) !== null),
                Action::make('viewEvidence')
                    ->label('Lihat')
                    ->icon(Heroicon::Eye)
                    ->modalHeading('Detail Bukti')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->schema(function (Action $action): array {
                        $record = $action->getRecord();

                        if (!$record instanceof EvidenceVersions) {
                            return [];
                        }

                        return self::evidenceViewSchema($record);
                    }),
                Action::make('editEvidence')
                    ->label('Edit')
                    ->icon(Heroicon::PencilSquare)
                    ->visible(fn(EvidenceVersions $record): bool => $this->record->canManageEvidence() &&
                        $record->evidence?->evidenceVersions()->count() === 1)
                    ->fillForm(fn(EvidenceVersions $record): array => [
                        'title' => $record->evidence?->title,
                        'description' => $record->evidence?->description,
                        'type' => $record->type,
                        'file_path' => $record->file_path,
                        'url_path' => $record->url_path,
                    ])
                    ->schema(self::evidenceFormSchema(requireFile: false))
                    ->action(function (EvidenceVersions $record, array $data): void {
                        $this->storeEvidence($data);

                        Notification::make()
                            ->title('Bukti berhasil diperbarui')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Belum ada bukti')
            ->emptyStateDescription('Tambahkan bukti pendukung untuk realisasi ini.')
            ->paginated(false);
    }

    /**
     * @return array<int, Component>
     */
    protected static function evidenceFormSchema(bool $requireFile): array
    {
        return [
            TextInput::make('title')
                ->label('Judul Bukti')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Deskripsi Bukti')
                ->columnSpanFull(),
            Select::make('type')
                ->label('Tipe Bukti')
                ->options([
                    'file' => 'File',
                    'url' => 'URL',
                ])
                ->required()
                ->live()
                ->native(false),
            FileUpload::make('file_path')
                ->label('File Bukti')
                ->disk('local')
                ->directory('evidences')
                ->visibility('private')
                ->visible(fn(Get $get): bool => $get('type') === 'file')
                ->required(fn(Get $get): bool => $requireFile && $get('type') === 'file'),
            TextInput::make('url_path')
                ->label('URL Bukti')
                ->url()
                ->visible(fn(Get $get): bool => $get('type') === 'url')
                ->required(fn(Get $get): bool => $get('type') === 'url'),
        ];
    }

    /**
     * @return array<int, TextEntry>
     */
    protected static function evidenceViewSchema(EvidenceVersions $record): array
    {
        $components = [
            Grid::make(3)
                ->schema([
                    TextEntry::make('version')
                        ->label('Versi')
                        ->state($record->version),
                    TextEntry::make('title')
                        ->label('Judul')
                        ->state($record->evidence?->title ?? '-'),
                    TextEntry::make('type')
                        ->label('Tipe')
                        ->state(match ($record->type) {
                            'file' => 'File',
                            'url' => 'URL',
                            default => $record->type,
                        }),
                ]),
            TextEntry::make('description')
                ->label('Deskripsi')
                ->columnSpanFull()
                ->state($record->evidence?->description ?? '-'),
        ];

        if ($record->type === 'file' && filled($record->file_path)) {
            $components[] = TextEntry::make('file_path')
                ->label('File')
                ->state(new HtmlString(
                    '<div class="flex flex-wrap gap-3">'
                    . '<a class="text-primary-600 underline" href="'
                    . e(route('evidence-files.preview', $record))
                    . '" target="_blank" rel="noopener">Pratinjau file</a>'
                    . '<a class="text-primary-600 underline" href="'
                    . e(route('evidence-files.download', $record))
                    . '" target="_blank" rel="noopener">Unduh file</a>'
                    . '</div>'
                ));
        }

        if ($record->type === 'url' && filled($record->url_path)) {
            $components[] = TextEntry::make('url_path')
                ->label('URL')
                ->state(new HtmlString(
                    '<a class="text-primary-600 underline" href="'
                    . e($record->url_path)
                    . '" target="_blank" rel="noopener">Pratinjau URL</a>'
                ));
        }

        return $components;
    }

    protected static function evidencePreviewUrl(EvidenceVersions $record): ?string
    {
        if ($record->type === 'url' && filled($record->url_path)) {
            return $record->url_path;
        }

        if ($record->type === 'file' && filled($record->file_path)) {
            return route('evidence-files.preview', $record);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function storeEvidence(array $data): void
    {
        $filePath = $data['file_path'] ?? null;

        if (is_array($filePath)) {
            $filePath = $filePath[0] ?? null;
        }

        EvidenceRecorded::dispatch(
            reference: $this->record,
            organizationUnitId: $this->record->organization_unit_id,
            title: (string) ($data['title'] ?? ''),
            description: $data['description'] ?? null,
            type: (string) ($data['type'] ?? 'url'),
            filePath: $filePath,
            urlPath: $data['url_path'] ?? null,
        );

        $this->record->refresh()->loadMissing('evidenceLink.evidence');
        $this->resetTable();
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $target = $this->record->target;

        if ($target === null) {
            return parent::getBreadcrumbs();
        }

        $targetLabel = $this->getTargetLabel($target);

        return [
            TargetResource::getUrl('index') => TargetResource::getNavigationLabel(),
            RealizationResource::getListUrl($target->id) => $targetLabel,
            'Detail Realisasi',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(RealizationResource::getListUrl($this->record->target_id))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
            EditAction::make()
                ->authorize(fn(Realization $record): bool => auth()->user()?->can('update', $record) ?? false),
            RealizationResource::submitAction(),
            DeleteAction::make()
                ->authorize(fn(Realization $record): bool => auth()->user()?->can('delete', $record) ?? false)
                ->successRedirectUrl(RealizationResource::getListUrl($this->record->target_id)),
            RealizationResource::approveAction(),
            RealizationResource::rejectAction(),
        ];
    }

    protected function getTargetLabel(Target $target): string
    {
        $target->loadMissing(['indicator:id,name', 'qualityPeriod:id,code']);

        return trim(($target->indicator?->name ?? '') . ' — ' . ($target->qualityPeriod?->code ?? ''));
    }
}
