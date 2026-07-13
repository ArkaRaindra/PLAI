<?php

namespace App\Notifications;

use App\Models\Evidences;
use App\Notifications\Channels\InAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvidenceRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Evidences $evidence,
        public readonly ?string $reason = null,
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', InAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
       $mail = (new MailMessage)
            ->subject('Evidence Ditolak')
            ->greeting("Halo {$notifiable->name},")
            ->line("Evidence \"{$this->evidence->title}\" yang anda ajukan ditolak dan perlu direvisi.");

        if (filled($this->reason)) {
            $mail->line("Alasan: {$this->reason}");
        }

        return $mail
            // ->action('Perbaiki Evidence', url("/super-admin/evidences/{$this->evidence->id}"))
            ->line('Silakan perbaiki lalu ajukan kembali');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toInApp(object $notifiable): array
    {
        $message = "Evidence \"{$this->evidence->title}\" yang Anda ajukan ditolak dan perlu direvisi.";

        if (filled($this->reason)) {
            $message = "Alasan: {$this->reason}";
        }

        return [
            'title' => 'Evidence Ditolak',
            'message' => $message,
            'action_url' => "/super-admin/evidences/{$this->evidence->id}",
        ];
    }
}
