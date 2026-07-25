<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions;

use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\CreateCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\EditCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\ListCorrectiveActions;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Pages\ViewCorrectiveAction;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas\CorrectiveActionForm;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas\CorrectiveActionInfolist;
use App\Filament\SuperAdmin\Resources\CorrectiveActions\Tables\CorrectiveActionsTable;
use App\Filament\SuperAdmin\Resources\CorrectiveActionUpdates\CorrectiveActionUpdateResource;
use App\Models\CorrectiveAction;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CorrectiveActionResource extends Resource
{
    protected static ?string $model = CorrectiveAction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Corrective Action';

    protected static ?string $pluralModelLabel = 'Corrective Actions';

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
            'verification' => 'Verifikasi',
            'closed' => 'Selesai',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'draft' => 'gray',
            'submitted' => 'warning',
            'verification' => 'primary',
            'closed' => 'success',
        ];
    }

    public static function startVerificationAction(): Action
    {
        return Action::make('startVerification')
            ->label('Mulai Verifikasi')
            ->icon(Heroicon::MagnifyingGlass)
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Mulai Verifikasi Corrective Action')
            ->modalDescription('Auditor akan memverifikasi bukti pelaksanaan tindak lanjut ini.')
            ->modalSubmitActionLabel('Ya, Mulai Verifikasi')
            ->visible(fn (CorrectiveAction $record): bool => $record->status === 'submitted')
            ->authorize(fn (): bool => Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (CorrectiveAction $record): void {
                try {
                    $record->startVerification();
                } catch (\RuntimeException $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title('Corrective action masuk tahap verifikasi')->success()->send();
            });
    }

    public static function approveAction(): Action
    {
        return Action::make('approveCorrectiveAction')
            ->label('Approve')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Approve Corrective Action')
            ->modalDescription('Bukti pelaksanaan dinyatakan memadai dan corrective action akan ditutup.')
            ->modalSubmitActionLabel('Ya, Approve')
            ->schema([
                Textarea::make('notes')->label('Catatan Verifikasi')->columnSpanFull(),
            ])
            ->visible(fn (CorrectiveAction $record): bool => $record->status === 'verification')
            ->authorize(fn (): bool => Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (CorrectiveAction $record, array $data): void {
                $record->approve($data['notes'] ?? null);

                Notification::make()->title('Corrective action disetujui dan ditutup')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('rejectCorrectiveAction')
            ->label('Reject')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Reject Corrective Action')
            ->modalDescription('Bukti pelaksanaan dinilai belum memadai dan akan dikembalikan ke PIC.')
            ->modalSubmitActionLabel('Ya, Reject')
            ->schema([
                Textarea::make('notes')->label('Alasan Penolakan')->required()->minLength(5)->columnSpanFull(),
            ])
            ->visible(fn (CorrectiveAction $record): bool => $record->status === 'verification')
            ->authorize(fn (): bool => Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (CorrectiveAction $record, array $data): void {
                $record->reject($data['notes']);

                Notification::make()->title('Corrective action dikembalikan ke PIC')->warning()->send();
            });
    }

    public static function progressTimelineAction(): Action
    {
        return Action::make('progressTimeline')
            ->label('Progress Timeline')
            ->icon(Heroicon::ChartBar)
            ->color('gray')
            ->url(fn (CorrectiveAction $record): string => CorrectiveActionUpdateResource::getListUrl($record->id));
    }

    public static function reopenAction(): Action
    {
        return Action::make('reopenCorrectiveAction')
            ->label('Reopen CAPA')
            ->icon(Heroicon::ArrowPath)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Buka Kembali Corrective Action')
            ->modalDescription('Corrective action yang sudah ditutup akan dibuka kembali untuk tindak lanjut lebih lanjut.')
            ->modalSubmitActionLabel('Ya, Reopen')
            ->schema([
                Textarea::make('notes')->label('Alasan Reopen')->required()->minLength(5)->columnSpanFull(),
            ])
            ->visible(fn (CorrectiveAction $record): bool => $record->status === 'closed')
            ->authorize(fn (): bool => Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (CorrectiveAction $record, array $data): void {
                $record->reopen($data['notes']);

                Notification::make()->title('Corrective action dibuka kembali')->warning()->send();
            });
    }
}
