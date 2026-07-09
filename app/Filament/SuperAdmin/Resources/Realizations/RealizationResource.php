<?php

namespace App\Filament\SuperAdmin\Resources\Realizations;

use App\Filament\SuperAdmin\Resources\Realizations\Pages\CreateRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\EditRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\ListRealizations;
use App\Filament\SuperAdmin\Resources\Realizations\Pages\ViewRealization;
use App\Filament\SuperAdmin\Resources\Realizations\Schemas\RealizationForm;
use App\Filament\SuperAdmin\Resources\Realizations\Schemas\RealizationInfolist;
use App\Filament\SuperAdmin\Resources\Realizations\Tables\RealizationsTable;
use App\Models\Realization;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RealizationResource extends Resource
{
    protected static ?string $model = Realization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Realisasi';

    protected static ?string $pluralModelLabel = 'Realisasi';

    protected static ?string $slug = 'realizations';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return RealizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RealizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RealizationsTable::configure($table);
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
            'index' => ListRealizations::route('/'),
            'create' => CreateRealization::route('/create'),
            'view' => ViewRealization::route('/{record}'),
            'edit' => EditRealization::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $targetId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'targetId' => $targetId,
        ]);
    }

    public static function getCreateUrl(int|string $targetId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'targetId' => $targetId,
        ]);
    }

    public static function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Pengajuan')
            ->icon(Heroicon::PaperAirplane)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Pengajuan Realisasi')
            ->modalDescription('Realisasi akan diajukan untuk disetujui. Data tidak dapat diubah lagi setelah diajukan.')
            ->modalSubmitActionLabel('Ya, Ajukan')
            ->authorize(fn (Realization $record): bool => Auth::user()?->can('submit', $record) ?? false)
            ->action(function (Realization $record): void {
                $record->update(['status' => 'submitted']);

                Notification::make()->title('Realisasi berhasil diajukan')->success()->send();
            });
    }

    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Setujui Realisasi')
            ->modalDescription('Realisasi akan disetujui dan tidak dapat diubah lagi setelahnya.')
            ->modalSubmitActionLabel('Ya, Setujui')
            ->authorize(fn (Realization $record): bool => Auth::user()?->can('approve', $record) ?? false)
            ->action(function (Realization $record): void {
                $record->update(['status' => 'approved']);

                Notification::make()->title('Realisasi disetujui')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Realisasi')
            ->modalSubmitActionLabel('Ya, Tolak')
            ->schema([
                Textarea::make('note_rejected')
                    ->label('Alasan Penolakan')
                    ->required()
                    ->minLength(5)
                    ->columnSpanFull(),
            ])
            ->authorize(fn (Realization $record): bool => Auth::user()?->can('reject', $record) ?? false)
            ->action(function (Realization $record, array $data): void {
                $record->update([
                    'status' => 'rejected',
                    'note_rejected' => $data['note_rejected'],
                ]);

                Notification::make()->title('Realisasi ditolak')->danger()->send();
            });
    }

    // /**
    //  * @return array<int, Action>
    //  */
    // public static function workflowActions(): array
    // {
    //     return [
    //         static::submitAction(),
    //         static::approveAction(),
    //         static::rejectAction(),
    //     ];
    // }
}
