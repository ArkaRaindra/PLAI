<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\CreateCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\EditCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\ListCorrectiveActions;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\ViewCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas\CorrectiveActionForm;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas\CorrectiveActionInfolist;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Tables\CorrectiveActionsTable;
use App\Models\CorrectiveAction;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CorrectiveActionResource extends Resource
{
    protected static ?string $model = CorrectiveAction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Corrective Action';

    protected static ?string $pluralModelLabel = 'corrective Actions';

    protected static ?string $slug = 'corrective-actions';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return CorrectiveActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CorrectiveActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorrectiveActionsTable::configure($table);
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
            'index' => ListCorrectiveActions::route('/'),
            'create' => CreateCorrectiveAction::route('/create'),
            'view' => ViewCorrectiveAction::route('/{record}'),
            'edit' => EditCorrectiveAction::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $auditFindingId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'auditFindingId' => $auditFindingId,
        ]);
    }

    public static function getCreateUrl(int|string $auditFindingId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'auditFindingId' => $auditFindingId,
        ]);
    }

    public static function statusLabels(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'draft' => 'gray',
            'submitted' => 'success',
        ];
    }

     public static function submitAction(): Action
    {
        return Action::make('submitCorrectiveAction')
            ->label('Submit Corrective Action')
            ->icon(Heroicon::PaperAirplane)
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Submit Corrective Action')
            ->modalDescription('Setelah disubmit, rencana tindak lanjut ini akan diajukan untuk verifikasi auditor.')
            ->modalSubmitActionLabel('Ya, Submit')
            ->visible(fn (CorrectiveAction $record): bool => $record->status === 'draft')
            ->action(function (CorrectiveAction $record): void {
                $record->submit();

                Notification::make()->title('Corrective action berhasil disubmit')->success()->send();
            });
    }
}
