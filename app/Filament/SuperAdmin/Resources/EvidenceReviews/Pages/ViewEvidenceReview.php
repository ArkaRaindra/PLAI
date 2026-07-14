<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Pages;

use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Models\EvidenceReview;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewEvidenceReview extends ViewRecord
{
    protected static string $resource = EvidenceReviewResource::class;

    protected function getHeaderActions(): array
    {
        /** @var EvidenceReview $record */
        $record = $this->record;
        $evidence = $record->evidence;
        $reviewStatus = $record->status;
        $evidenceStatus = $evidence?->workflowInstance?->current_status;

        $actions = [
            Action::make('back')
                ->label('Kembali')
                ->url(fn (): string => EvidenceReviewResource::getListUrl())
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];

        if ($reviewStatus === 'pending' && $evidenceStatus === 'submitted') {
            $actions[] = Action::make('startReview')
                ->label('Mulai Review')
                ->icon(Heroicon::MagnifyingGlass)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Mulai Review Evidence')
                ->modalDescription('Evidence akan masuk tahap review.')
                ->modalSubmitActionLabel('Ya, Mulai Review')
                ->authorize(fn (): bool => Auth::user()?->can('evidence.review') ?? false)
                ->action(function (EvidenceReview $record) use ($evidence): void {
                    $record->update(['status' => 'review']);
                    $evidence->workflowInstance->transitionTo('review');

                    Notification::make()->title('Evidence masuk tahap review')->success()->send();
                });
        }

        if ($reviewStatus === 'review') {
            $actions[] = Action::make('approve')
                ->label('Setujui')
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Evidence')
                ->modalSubmitActionLabel('Ya, Setujui')
                ->authorize(fn (): bool => Auth::user()?->can('evidence.review') ?? false)
                ->action(function (EvidenceReview $record) use ($evidence): void {
                    $record->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                    ]);
                    $evidence->workflowInstance->transitionTo('approved', $record->review_notes);

                    Notification::make()->title('Evidence disetujui')->success()->send();
                });

            $actions[] = Action::make('reject')
                ->label('Tolak')
                ->icon(Heroicon::XCircle)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Evidence')
                ->modalDescription('Evidence akan dikembalikan ke status draft untuk direvisi.')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->form([
                    Textarea::make('notes')->label('Alasan Penolakan')->required()->minLength(5)->columnSpanFull(),
                ])
                ->authorize(fn (): bool => Auth::user()?->can('evidence.review') ?? false)
                ->action(function (EvidenceReview $record, array $data) use ($evidence): void {
                    $record->update([
                        'status' => 'rejected',
                        'review_notes' => $data['notes'],
                        'reviewed_at' => now(),
                    ]);
                    $evidence->workflowInstance->transitionTo('rejected', $data['notes']);

                    Notification::make()->title('Evidence ditolak')->danger()->send();
                });
        }

        return $actions;
    }
}
