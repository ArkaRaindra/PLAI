@php
    $entries = $this->timelineEntries();
    $latest = $this->latestProgress();
@endphp

<x-filament-panels::page>
    @if ($this->correctiveAction === null)
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Corrective Action tidak ditemukan.
            </p>
        </x-filament::section>
    @else
        <x-filament::section>
            <x-slot name="heading">
                Progress Saat Ini
            </x-slot>

            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        {{ $this->correctiveAction->auditFinding?->title }}
                    </span>
                    <span class="font-semibold">{{ $latest }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-white/10">
                    <div
                        class="h-2 rounded-full {{ $latest >= 100 ? 'bg-success-500' : 'bg-primary-500' }}"
                        style="width: {{ max(0, min(100, $latest)) }}%"
                    ></div>
                </div>
                @if ($latest >= 100)
                    <p class="text-xs text-success-600 dark:text-success-400">
                        Progress telah mencapai 100% — siap untuk tahap verifikasi.
                    </p>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Progress Timeline
            </x-slot>

            @if ($entries->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Belum ada progress yang tercatat.
                </p>
            @else
                <div class="space-y-6">
                    @foreach ($entries as $entry)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-xs font-semibold text-primary-700 dark:bg-primary-500/20 dark:text-primary-300">
                                    {{ $entry->progress_percentage }}%
                                </span>
                                @if (! $loop->last)
                                    <span class="mt-1 w-px flex-1 bg-gray-200 dark:bg-white/10"></span>
                                @endif
                            </div>

                            <div class="flex-1 pb-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium">
                                        {{ $entry->updater?->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $entry->updated_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $entry->description }}
                                </p>

                                @php
                                    $evidence = $entry->evidenceLink?->evidence;
                                @endphp

                                @if ($evidence !== null)
                                    <div class="mt-2">
                                        @if ($evidence->type === 'file' && filled($evidence->file_path))
                                            
                                                href="{{ route('evidences.download', $evidence) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="text-xs text-primary-600 underline dark:text-primary-400"
                                            >
                                                📎 Lihat Bukti (File)
                                            </a>
                                        @elseif ($evidence->type === 'url' && filled($evidence->url_path))
                                            
                                                href="{{ $evidence->url_path }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="text-xs text-primary-600 underline dark:text-primary-400"
                                            >
                                                🔗 Lihat Bukti (URL)
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>