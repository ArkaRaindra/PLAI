<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\CreateEvidenceReview;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\EditEvidenceReview;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\ListEvidenceReviews;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages\ViewEvidenceReview;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas\EvidenceReviewForm;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas\EvidenceReviewInfolist;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\Tables\EvidenceReviewsTable;
use App\Models\EvidenceReview;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EvidenceReviewResource extends Resource
{
    protected static ?string $model = EvidenceReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Review Evidence';

    protected static ?string $pluralModelLabel = 'Review Evidence';

    protected static ?string $slug = 'evidence-reviews';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return EvidenceReviewForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvidenceReviewInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvidenceReviewsTable::configure($table);
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
            'index' => ListEvidenceReviews::route('/'),
            'create' => CreateEvidenceReview::route('/create'),
            'view' => ViewEvidenceReview::route('/{record}'),
            'edit' => EditEvidenceReview::route('/{record}/edit'),
        ];
    }

    public static function getListUrl(int|string $evidenceId): string
    {
        return static::getUrl('index').'?'.http_build_query([
            'evidenceId' => $evidenceId,
        ]);
    }

    public static function getCreateUrl(int|string $evidenceId): string
    {
        return static::getUrl('create').'?'.http_build_query([
            'evidenceId' => $evidenceId,
        ]);
    }

    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->icon(Heroicon::CheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Setujui Evidence')
            ->modalDescription('Evidence akan ditandai sebagai disetujui oleh reviewer.')
            ->modalSubmitActionLabel('Ya, Setujui')
            ->schema([
                Textarea::make('review_notes')
                    ->label('Catatan Review')
                    ->columnSpanFull(),
            ])
            ->fillForm(fn (EvidenceReview $record): array => [
                'review_notes' => $record->review_notes,
            ])
            ->authorize(fn (EvidenceReview $record): bool => Auth::user()?->can('approve', $record) ?? false)
            ->action(function (EvidenceReview $record, array $data): void {
                $record->update([
                    'status' => 'approved',
                    'review_notes' => $data['review_notes'] ?? $record->review_notes,
                ]);

                Notification::make()->title('Evidence disetujui')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Tolak')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Tolak Evidence')
            ->modalSubmitActionLabel('Ya, Tolak')
            ->schema([
                Textarea::make('review_notes')
                    ->label('Catatan Review')
                    ->required()
                    ->minLength(5)
                    ->columnSpanFull(),
            ])
            ->fillForm(fn (EvidenceReview $record): array => [
                'review_notes' => $record->review_notes,
            ])
            ->authorize(fn (EvidenceReview $record): bool => Auth::user()?->can('reject', $record) ?? false)
            ->action(function (EvidenceReview $record, array $data): void {
                $record->update([
                    'status' => 'rejected',
                    'review_notes' => $data['review_notes'],
                ]);

                Notification::make()->title('Evidence ditolak')->danger()->send();
            });
    }

    public static function requestRevisionAction(): Action
    {
        return Action::make('requestRevision')
            ->label('Minta Revisi')
            ->icon(Heroicon::ArrowUturnLeft)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Minta Revisi Evidence')
            ->modalDescription('Uploader akan diminta memperbaiki evidence berdasarkan catatan review.')
            ->modalSubmitActionLabel('Ya, Minta Revisi')
            ->schema([
                Textarea::make('review_notes')
                    ->label('Catatan Review')
                    ->required()
                    ->minLength(5)
                    ->columnSpanFull(),
            ])
            ->fillForm(fn (EvidenceReview $record): array => [
                'review_notes' => $record->review_notes,
            ])
            ->authorize(fn (EvidenceReview $record): bool => Auth::user()?->can('requestRevision', $record) ?? false)
            ->action(function (EvidenceReview $record, array $data): void {
                $record->update([
                    'status' => 'revision_needed',
                    'review_notes' => $data['review_notes'],
                ]);

                Notification::make()->title('Revisi diminta ke uploader')->warning()->send();
            });
    }

    public static function reopenAction(): Action
    {
        return Action::make('reopen')
            ->label('Buka Kembali')
            ->icon(Heroicon::ArrowPath)
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Buka Kembali Review')
            ->modalDescription('Review akan dikembalikan ke status menunggu untuk diperiksa ulang oleh reviewer.')
            ->modalSubmitActionLabel('Ya, Buka Kembali')
            ->authorize(fn (EvidenceReview $record): bool => Auth::user()?->can('reopen', $record) ?? false)
            ->action(function (EvidenceReview $record): void {
                $record->update(['status' => 'pending']);

                Notification::make()->title('Review dibuka kembali')->success()->send();
            });
    }
}
