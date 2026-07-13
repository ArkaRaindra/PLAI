<div
    class="relative ms-3"
    x-data="{ open: false }"
    x-on:click.outside="open = false"
>
    <button
        type="button"
        x-on:click="open = ! open"
        class="fi-topbar-item-btn relative"
        title="Notifikasi"
        aria-label="Notifikasi"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if ($this->unreadCount > 0)
            <span class="absolute -end-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger-500 px-1 text-[10px] font-semibold leading-none text-white">
                {{ $this->unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition
        class="absolute end-0 top-full z-[60] mt-2 w-80 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        style="display: none;"
    >
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-2 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifikasi</span>

            @if ($this->unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-xs font-medium text-primary-600 hover:underline dark:text-primary-400"
                >
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($this->notifications as $notification)
                <button
                    type="button"
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="block w-full border-b border-gray-100 px-4 py-3 text-left transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5 {{ $notification->is_read ? '' : 'bg-primary-50/60 dark:bg-primary-500/10' }}"
                >
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $notification->title }}
                    </div>
                    <div class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $notification->message }}
                    </div>
                    <div class="mt-1 text-[10px] text-gray-400">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </button>
            @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">
                    Tidak ada notifikasi
                </div>
            @endforelse
        </div>
    </div>
</div>
