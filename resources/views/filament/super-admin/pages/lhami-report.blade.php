@php
    $summary = $this->summary()
    $assignmentsStatusLabels = static::workflowStatusLabels();
    $categoryLables = static::categoryLabels();
    $severityLabels = static::severityLabels();
@endphp

<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Ringkasan Audit
        </x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Assignment</p>
                <p class="text-2xl font-semibold">{{ $summary['assignments_total'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ $summary['completion_percentage'] }}% selesai
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Temuan</p>
                <p class="text-2xl font-semibold">{{ $summary['findings_total'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ $summary['findings_by_status']['open'] ?? 0 }} terbuka ·
                    {{ $summary['findings_by_status']['closed'] ?? 0 }} ditutup
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Status Assignment</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($assignmentStatusLabels as $key => $label)
                        <x-filament::badge :color="match ($key) {
                            'open' => 'gray',
                            'assigned' => 'info',
                            'corrective_action' => 'warning',
                            'verification' => 'primary',
                            'closed' => 'success',
                            default => 'gray',
                        }">
                            {{ $label }}: {{ $summary['assignments_by_status'][$key] ?? 0 }}
                        </x-filament::badge>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Temuan per Kategori</p>
                <div class="flex flex-wrap gap-1">
                    @foreach ($categoryLabels as $key => $label)
                        <x-filament::badge :color="match ($key) {
                            'kts' => 'danger',
                            'ofi' => 'info',
                            default => 'gray',
                        }">
                            {{ $label }}: {{ $summary['findings_by_category'][$key] ?? 0 }}
                        </x-filament::badge>
                    @endforeach
                    @foreach ($severityLabels as $key => $label)
                        <x-filament::badge :color="$key === 'major' ? 'danger' : 'warning'">
                            {{ $label }}: {{ $summary['findings_by_severity'][$key] ?? 0 }}
                        </x-filament::badge>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page
