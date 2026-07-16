<?php

namespace App\Notifications;

use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvidenceSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Evidences $evidence
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
            ->subject('Evidence Baru Menunggu Review')
            ->greeting("Halo {$notifiable->name},")
            ->line("Evidence \"{$this->evidence->title}\" telah diajukan dan menunggu review Anda")
            // ->action('Tinjau Evidence', url("/super-admin/evidences-reviews/{$this->evidence->id}"))
            ->line('Mohon segera ditindaklanjuti');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Evidence Baru Menunggu Review')
            ->body("Evidence \"{$this->evidence->title}\" telah diajukan dan menunggu review Anda")
            ->icon('heroicon-o-magnifying-glass')
            ->iconColor('warning')
            ->warning()
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Tinjau Evidence')
                    ->url("/super-admin/evidences-reviews/{$this->evidence->id}")
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
