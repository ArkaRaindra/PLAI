<?php

namespace App\Notifications;

use App\Models\Evidences;
use App\Notifications\Channels\InAppChannel;
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
        return ['mail', InAppChannel::class];
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
            // ->action('Tinjau Evidence', url("/super-admin/evidences/{$this->evidence->id}"))
            ->line('Mohon segera ditindaklanjuti');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toInApp(object $notifiable): array
    {
        return [
            'title' => 'Evidence Baru Menunggu Review',
            'message' => "Evidence \"{$this->evidence->title}\" telah diajukan dan menunggu review Anda",
            'action_url' => "/super-admin/evidences/{$this->evidence->id}",
        ];
    }
}
