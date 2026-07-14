<?php

namespace App\Notifications;

use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvidenceApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Evidences $evidence,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Evidence Disetujui')
            ->greeting("Halo {$notifiable->name}")
            ->line("Evidence \"{$this->evidence->title}\" yang Anda ajukan telah disetujui.")
            // ->action('Lihat Evidence', url("/super-admin/evidences/{$this->evidence->id}"))
            ->line('Terimakasih aats kontribusi Anda');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Evidence Disetujui')
            ->body("Evidence \"{$this->evidence->title}\" yang Anda ajukan telah disetujui.")
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success()
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Lihat Evidence')
                    ->url("/super-admin/evidences/{$this->evidence->id}")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
