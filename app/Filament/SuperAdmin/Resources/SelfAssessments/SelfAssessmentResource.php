<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments;

use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\CreateSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\EditSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\ListSelfAssessments;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\ViewSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas\SelfAssessmentForm;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas\SelfAssessmentInfolist;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Tables\SelfAssessmentsTable;
use App\Models\SelfAssessment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SelfAssessmentResource extends Resource
{
    protected static ?string $model = SelfAssessment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Self Assessment';

    protected static ?string $pluralModelLabel = 'Self Assessment';

    protected static ?string $slug = 'self-assessments';

    protected static string|\UnitEnum|null $navigationGroup = 'Indikator';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Self Assessemnt';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(SelfAssessmentForm::schema());
    }

    public static function infolist(Schema $schema): Schema
    {
        return SelfAssessmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SelfAssessmentsTable::configure($table);
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
            'index' => ListSelfAssessments::route('/'),
            'create' => CreateSelfAssessment::route('/create'),
            'view' => ViewSelfAssessment::route('/{record}'),
            'edit' => EditSelfAssessment::route('/{record}/edit'),
        ];
    }

    public static function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajukan')
            ->icon(Heroicon::PaperAirplane)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Ajukan Self Assessment')
            ->modalDescription('Self assessment akan diajukan untuk disetujui. Data tidak dapat diubah lagi setelah diajukan.')
            ->modalSubmitActionLabel('Ya, Ajukan')
            ->authorize(fn (SelfAssessment $record): bool => Auth::user()?->can('submit', $record) ?? false)
            ->action(function (SelfAssessment $record): void {
                $record->update(['status' => 'submitted']);

                Notification::make()->title('Self assessment berhasil diajukan')->success()->send();
            });
    }

    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Setujui Self Assessment')
            ->modalDescription('Self assessment akan disetujui, Final Score dikunci, dan tidak dapat diubah lagi setelahnya.')
            ->modalSubmitActionLabel('Ya, Setujui')
            ->authorize(fn (SelfAssessment $record): bool => Auth::user()?->can('approve', $record) ?? false)
            ->action(function (SelfAssessment $record): void {
                $record->update(['status' => 'approved']);

                Notification::make()->title('Self assessment disetujui')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Self Assessment')
            ->modalSubmitActionLabel('Ya, Tolak')
            ->schema([
                Textarea::make('note_rejected')
                    ->label('Alasan Penolakan')
                    ->required()
                    ->minLength(5)
                    ->columnSpanFull(),
            ])
            ->authorize(fn (SelfAssessment $record): bool => Auth::user()?->can('reject', $record) ?? false)
            ->action(function (SelfAssessment $record, array $data): void {
                $record->update([
                    'status' => 'rejected',
                    'note_rejected' => $data['note_rejected'],
                ]);

                Notification::make()->title('Self assessment ditolak')->danger()->send();
            });
    }
}
