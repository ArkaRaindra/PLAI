<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistTemplates;

use App\Filament\SuperAdmin\Resources\AuditChecklistTemplateItems\AuditChecklistTemplateItemResource;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\CreateAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\EditAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\ListAuditChecklistTemplates;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Pages\ViewAuditChecklistTemplate;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateForm;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Schemas\AuditChecklistTemplateInfolist;
use App\Filament\SuperAdmin\Resources\AuditChecklistTemplates\Tables\AuditChecklistTemplatesTable;
use App\Models\AuditChecklistTemplate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditChecklistTemplateResource extends Resource
{
    protected static ?string $model = AuditChecklistTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Checklist Template';

    protected static ?string $modelLabel = 'Checklist Template';

    protected static ?string $pluralModelLabel = 'Checklist Template';

    protected static string|\UnitEnum|null $navigationGroup = 'AMI';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [
            static::getRouteBaseName().'.*',
            AuditChecklistTemplateItemResource::getRouteBaseName().'.*',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AuditChecklistTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditChecklistTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditChecklistTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditChecklistTemplates::route('/'),
            'create' => CreateAuditChecklistTemplate::route('/create'),
            'view' => ViewAuditChecklistTemplate::route('/{record}'),
            'edit' => EditAuditChecklistTemplate::route('/{record}/edit'),
        ];
    }

    public static function activateAction(): Action
    {
        return Action::make('activate')
            ->label('Aktifkan')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription('Versi lain dari checklist ini (jika ada) akan dinonaktifkan.')
            ->visible(fn (AuditChecklistTemplate $record): bool => ! $record->is_active)
            ->action(function (AuditChecklistTemplate $record): void {
                DB::transaction(function () use ($record): void {
                    AuditChecklistTemplate::query()
                        ->where('code', $record->code)
                        ->where('id', '!=', $record->id)
                        ->update(['is_active' => false]);

                    $record->update(['is_active' => true]);
                });

                Notification::make()->title('Checklist template diaktifkan')->success()->send();
            });
    }

    public static function newVersionAction(): Action
    {
        return Action::make('newVersion')
            ->label('Buat Versi Baru')
            ->icon(Heroicon::DocumentDuplicate)
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription('Semua item pada versi ini akan disalin ke versi baru.')
            ->action(function (AuditChecklistTemplate $record): void {
                $newVersion = DB::transaction(function () use ($record): AuditChecklistTemplate {
                    $nextVersion = app(\App\Services\Versioning\VersionGeneratorService::class)->next(
                        AuditChecklistTemplate::class,
                        'code',
                        $record->code,
                    );

                    $new = AuditChecklistTemplate::query()->create([
                        'code' => $record->code,
                        'name' => $record->name,
                        'version' => $nextVersion,
                        'description' => $record->description,
                        'is_active' => false,
                    ]);

                    foreach ($record->items()->get() as $item) {
                        $new->items()->create([
                            'standard_version_id' => $item->standard_version_id,
                            'question' => $item->question,
                            'sequence' => $item->sequence,
                        ]);
                    }

                    return $new;
                });

                Notification::make()
                    ->title("Versi baru {$newVersion->version} berhasil dibuat")
                    ->success()
                    ->send();
            });
    }
}