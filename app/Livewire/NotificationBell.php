<?php

namespace App\Livewire;

use App\Models\Notification as AppNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function getUnreadCountProperty(): int
    {
        return AppNotification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    public function getNotificationsProperty(): Collection
    {
        return AppNotification::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = AppNotification::query()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if (filled($notification->action_url)) {
            return redirect($notification->action_url);
        }

        return redirect()->back();
    }

    public function markAllAsRead(): void
    {
        AppNotification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.notification-bell');
    }
}
