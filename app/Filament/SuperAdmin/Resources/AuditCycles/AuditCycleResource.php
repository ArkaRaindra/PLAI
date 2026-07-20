<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles;

use App\Filament\SuperAdmin\Resources\AuditAssignments\AuditAssignmentResource;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\CreateAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\EditAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\ListAuditCycles;
use App\Filament\SuperAdmin\Resources\AuditCycles\Pages\ViewAuditCycle;
use App\Filament\SuperAdmin\Resources\AuditCycles\Schemas\AuditCycleForm;
use App\Filament\SuperAdmin\Resources\AuditCycles\Schemas\AuditCycleInfolist;
use App\Filament\SuperAdmin\Resources\AuditCycles\Tables\AuditCyclesTable;
use App\Models\AuditCycle;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AuditCycleResource extends Resource
{
    protected static ?string $model = AuditCycle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Audit Cycle';

    protected static ?string $pluralModelLabel = 'Audit Cycle';

    protected static string|UnitEnum|null $navigationGroup = 'AMI';

    protected static ?string $navigationLabel = 'Audit Cycle';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationItemActiveRoutePattern(): string|array
    {
        return [
            static::getRouteBaseName().'.*',
            AuditAssignmentResource::getRouteBaseName().'.*',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AuditCycleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditCycleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditCyclesTable::configure($table);
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
            'index' => ListAuditCycles::route('/'),
            'create' => CreateAuditCycle::route('/create'),
            'view' => ViewAuditCycle::route('/{record}'),
            'edit' => EditAuditCycle::route('/{record}/edit'),
        ];
    }

    public static function activateAction(): Action
    {
        return Action::make('activateCycle')
            ->label('Aktifkan')
            ->icon(Heroicon::Play)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (AuditCycle $record): bool => $record->canTransitionTo('active') || Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (AuditCycle $record): void {
                $record->update(['status' => 'active']);

                Notification::make()->title('Audit cycle diaktifkan')->success()->send();
            });
    }

    public static function closeAction(): Action
    {
        return Action::make('closeCycle')
            ->label('Tutup Siklus')
            ->icon(Heroicon::Stop)
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Siklus audit yang ditutup tidak dapat menerima penugasan baru.')
            ->visible(fn (AuditCycle $record): bool => $record->canTransitionTo('closed') || Auth::user()?->hasRole('super-admin') ?? false)
            ->action(function (AuditCycle $record): void {
                $record->update(['status' => 'closed']);

                Notification::make()->title('Audit cycle ditutup')->success()->send();
            });
    }
}
